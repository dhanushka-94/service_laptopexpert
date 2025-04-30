<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceJobRequest;
use App\Models\Customer;
use App\Models\ServiceJob;
use App\Models\User;
use Illuminate\Http\Request;

class ServiceJobController extends Controller
{
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
        
        $job->update(['status' => $request->status]);
        
        return redirect()->back()->with('success', 'Job status updated successfully.');
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
