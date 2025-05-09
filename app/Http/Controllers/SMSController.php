<?php

namespace App\Http\Controllers;

use App\Models\ServiceJob;
use App\Services\SMSService;
use App\Http\Controllers\ShareableController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SMSController extends Controller
{
    protected $smsService;

    public function __construct(SMSService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Send a job note summary via SMS.
     */
    public function sendJobNoteSMS(Request $request, ServiceJob $job)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Load job relations
        $job->load(['customer', 'technician', 'notes' => function($query) {
            $query->where('is_private', false)->latest();
        }]);

        // Prepare the message
        $message = "Job #" . $job->job_id . " Status: " . $job->status . "\n";
        
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
        $phoneNumber = $request->input('phone_number');
        $contactDetails = [
            'first_name' => $job->customer->name,
            'email' => $job->customer->email,
        ];
        
        $options = [
            'service_job_id' => $job->id,
            'customer_id' => $job->customer_id,
            'type' => 'manual',
        ];

        $result = $this->smsService->send($phoneNumber, $message, $contactDetails, $options);

        if ($result['success']) {
            return redirect()->back()->with('success', 'SMS sent successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to send SMS. Please try again later.');
        }
    }
} 