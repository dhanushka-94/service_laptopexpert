<?php

namespace App\Http\Controllers;

use App\Models\SmsLog;
use App\Models\Customer;
use App\Models\ServiceJob;
use App\Services\SMSService;
use Illuminate\Http\Request;

class SmsLogController extends Controller
{
    /**
     * Display a listing of SMS logs with filtering options.
     */
    public function index(Request $request)
    {
        $query = SmsLog::with(['customer', 'serviceJob']);
        
        // Filter by phone number
        if ($request->filled('phone_number')) {
            $phone = $request->phone_number;
            $query->where('phone_number', 'like', "%{$phone}%");
        }
        
        // Filter by customer
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }
        
        // Filter by service job
        if ($request->filled('service_job_id')) {
            $query->where('service_job_id', $request->service_job_id);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Search in message content
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('message', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }
        
        // Get logs with pagination
        $logs = $query->latest()->paginate(15);
        
        // Get customers and service jobs for dropdowns
        $customers = Customer::orderBy('name')->get();
        $serviceJobs = ServiceJob::orderBy('created_at', 'desc')->get();
        
        return view('sms-logs.index', compact('logs', 'customers', 'serviceJobs'));
    }
    
    /**
     * Show a specific SMS log.
     */
    public function show(SmsLog $smsLog)
    {
        $smsLog->load(['customer', 'serviceJob']);
        
        return view('sms-logs.show', compact('smsLog'));
    }
    
    /**
     * Resend a failed or previously sent SMS.
     */
    public function resend(SmsLog $smsLog)
    {
        // Get the SMS service
        $smsService = app(SMSService::class);
        
        // Set up contact details if customer exists
        $contactDetails = [];
        if ($smsLog->customer) {
            $contactDetails = [
                'first_name' => $smsLog->customer->name,
                'email' => $smsLog->customer->email,
            ];
        }
        
        // Prepare options
        $options = [
            'service_job_id' => $smsLog->service_job_id,
            'customer_id' => $smsLog->customer_id,
            'type' => $smsLog->type . '_resend',
        ];
        
        // Resend the SMS
        $result = $smsService->send($smsLog->phone_number, $smsLog->message, $contactDetails, $options);
        
        if ($result['success']) {
            return redirect()->back()->with('success', 'SMS resent successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to resend SMS. Please try again later.');
        }
    }
} 