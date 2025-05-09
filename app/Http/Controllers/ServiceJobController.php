<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceJobRequest;
use App\Models\Customer;
use App\Models\ServiceJob;
use App\Models\User;
use App\Services\SMSService;
use App\Http\Controllers\ShareableController;
use Illuminate\Http\Request;

class ServiceJobController extends Controller
{
    protected $smsService;

    /**
     * Create a new controller instance.
     */
    public function __construct(SMSService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ServiceJob::with(['customer', 'technician']);
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by technician
        if ($request->filled('technician_id')) {
            $query->where('technician_id', $request->technician_id);
        }
        
        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Search by job ID, customer name, device type
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('job_id', 'like', "%{$search}%")
                  ->orWhere('device_type', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        $jobs = $query->latest()->paginate(10);
        $technicians = User::role('technician')->get();
        
        return view('jobs.index', compact('jobs', 'technicians'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::all();
        $technicians = User::role('technician')->get();
        
        return view('jobs.create', compact('customers', 'technicians'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ServiceJobRequest $request)
    {
        $job = ServiceJob::create($request->validated());
        
        // Send SMS notification about new job
        $this->sendStatusUpdateSMS($job);
        
        if ($request->has('print_receipt')) {
            return redirect()->route('jobs.print', $job);
        }
        
        return redirect()->route('jobs.show', $job)
            ->with('success', 'Job created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ServiceJob $job)
    {
        $job->load(['customer', 'technician', 'notes.user']);
        $technicians = User::role('technician')->get();
        
        return view('jobs.show', compact('job', 'technicians'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServiceJob $job)
    {
        $customers = Customer::all();
        $technicians = User::role('technician')->get();
        
        return view('jobs.edit', compact('job', 'customers', 'technicians'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ServiceJobRequest $request, ServiceJob $job)
    {
        $job->update($request->validated());
        
        return redirect()->route('jobs.show', $job)
            ->with('success', 'Job updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceJob $job)
    {
        $job->delete();
        
        return redirect()->route('jobs.index')
            ->with('success', 'Job deleted successfully.');
    }
    
    /**
     * Update the job status.
     */
    public function updateStatus(Request $request, ServiceJob $job)
    {
        $request->validate([
            'status' => 'required|in:Pending,In Progress,Awaiting Parts,Repaired,Delivered,Canceled',
        ]);
        
        $oldStatus = $job->status;
        $newStatus = $request->status;
        
        // Update the job status
        $job->update(['status' => $newStatus]);
        
        // If status has changed, send SMS notification
        if ($oldStatus !== $newStatus) {
            $this->sendStatusUpdateSMS($job);
        }
        
        return redirect()->back()->with('success', 'Job status updated successfully.');
    }
    
    /**
     * Send SMS notification about status update to customer.
     */
    private function sendStatusUpdateSMS(ServiceJob $job)
    {
        // Load related data
        $job->load(['customer', 'technician', 'notes' => function($query) {
            $query->where('is_private', false)->latest();
        }]);
        
        // Check if customer has a phone number
        if (empty($job->customer->phone_1)) {
            return;
        }
        
        // Prepare the message
        $message = "Status Update: Job #" . $job->job_id . " is now " . $job->status . "\n";
        
        // Add device info
        $message .= $job->device_type;
        if ($job->brand) $message .= " " . $job->brand;
        if ($job->model) $message .= " " . $job->model;
        $message .= "\n";
        
        // Add latest note if available
        if ($job->notes->count() > 0) {
            $message .= "Latest update: " . $job->notes->first()->note . "\n";
        }
        
        if ($job->final_cost) {
            $message .= "Cost: LKR " . number_format($job->final_cost, 2) . "\n";
        }
        
        // Add shareable link if available
        if ($job->shareableToken) {
            $message .= "View details: " . url('/share/' . $job->shareableToken->token) . "\n";
        } else {
            // Generate a token first
            $shareableController = app(ShareableController::class);
            $response = $shareableController->generateLink($job);
            $data = json_decode($response->getContent(), true);
            if (isset($data['url'])) {
                $message .= "View details: " . $data['url'] . "\n";
            }
        }
        
        // Add footer
        $message .= "Thank you for choosing Laptop Experts Service Center.";
        
        // Send the SMS
        $phoneNumber = $job->customer->phone_1;
        $contactDetails = [
            'first_name' => $job->customer->name,
            'email' => $job->customer->email,
        ];
        
        $options = [
            'service_job_id' => $job->id,
            'customer_id' => $job->customer_id,
            'type' => 'status_update',
        ];
        
        $this->smsService->send($phoneNumber, $message, $contactDetails, $options);
    }
    
    /**
     * Assign technician to job.
     */
    public function assignTechnician(Request $request, ServiceJob $job)
    {
        $request->validate([
            'technician_id' => 'required|exists:users,id',
        ]);
        
        $job->update(['technician_id' => $request->technician_id]);
        
        return redirect()->back()->with('success', 'Technician assigned successfully.');
    }
}
