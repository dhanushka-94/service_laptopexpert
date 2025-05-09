<?php

namespace App\Services;

use App\Models\SmsLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SMSService
{
    protected $apiUrl;
    protected $userId;
    protected $apiKey;
    protected $senderId;

    public function __construct()
    {
        $this->apiUrl = 'https://app.notify.lk/api/v1/send';
        $this->userId = config('services.notifylk.user_id');
        $this->apiKey = config('services.notifylk.api_key');
        $this->senderId = config('services.notifylk.sender_id', 'NotifyDEMO');
    }

    /**
     * Send SMS to a phone number
     *
     * @param string $phoneNumber The recipient's phone number in format 9471XXXXXXX
     * @param string $message The message to send (max 621 chars)
     * @param array $contactDetails Optional contact details
     * @param array $options Additional options like service_job_id, customer_id, type
     * @return array Response from API
     */
    public function send($phoneNumber, $message, array $contactDetails = [], array $options = [])
    {
        // Format phone number if needed (remove +94, 0 prefix, spaces, etc.)
        $phoneNumber = $this->formatPhoneNumber($phoneNumber);
        
        // Truncate message if longer than limit
        if (strlen($message) > 621) {
            $message = substr($message, 0, 618) . '...';
        }

        // Prepare request data
        $data = [
            'user_id' => $this->userId,
            'api_key' => $this->apiKey,
            'sender_id' => $this->senderId,
            'to' => $phoneNumber,
            'message' => $message,
        ];

        // Add optional contact details if provided
        if (!empty($contactDetails['first_name'])) {
            $data['contact_fname'] = $contactDetails['first_name'];
        }
        
        if (!empty($contactDetails['last_name'])) {
            $data['contact_lname'] = $contactDetails['last_name'];
        }
        
        if (!empty($contactDetails['email'])) {
            $data['contact_email'] = $contactDetails['email'];
        }
        
        if (!empty($contactDetails['address'])) {
            $data['contact_address'] = $contactDetails['address'];
        }

        try {
            // Make API request
            $response = Http::get($this->apiUrl, $data);
            
            // Log response for debugging
            Log::info('SMS API Response', ['response' => $response->json()]);
            
            $result = [
                'success' => $response->successful() && isset($response['status']) && $response['status'] === 'success',
                'response' => $response->json(),
            ];
            
            // Log to database
            $this->logSms(
                $phoneNumber, 
                $message, 
                $result['success'] ? 'sent' : 'failed', 
                json_encode($response->json()), 
                $options['service_job_id'] ?? null,
                $options['customer_id'] ?? null,
                $options['type'] ?? 'status_update'
            );
            
            return $result;
        } catch (\Exception $e) {
            Log::error('SMS Sending Error', ['error' => $e->getMessage()]);
            
            // Log failed attempt to database
            $this->logSms(
                $phoneNumber, 
                $message, 
                'failed', 
                $e->getMessage(),
                $options['service_job_id'] ?? null,
                $options['customer_id'] ?? null,
                $options['type'] ?? 'status_update'
            );
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Format phone number to required format (9471XXXXXXX)
     */
    protected function formatPhoneNumber($phoneNumber)
    {
        // Remove any non-numeric characters
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // If starts with 0, remove it
        if (substr($phoneNumber, 0, 1) === '0') {
            $phoneNumber = substr($phoneNumber, 1);
        }
        
        // If starts with 94, keep it as is
        if (substr($phoneNumber, 0, 2) === '94') {
            return $phoneNumber;
        }
        
        // Otherwise, prepend 94 (Sri Lanka country code)
        return '94' . $phoneNumber;
    }
    
    /**
     * Log SMS to database
     */
    protected function logSms($phoneNumber, $message, $status, $response, $serviceJobId = null, $customerId = null, $type = 'status_update')
    {
        SmsLog::create([
            'service_job_id' => $serviceJobId,
            'customer_id' => $customerId,
            'phone_number' => $phoneNumber,
            'message' => $message,
            'status' => $status,
            'response' => $response,
            'type' => $type,
            'triggered_by' => Auth::id() ?? 'system',
        ]);
    }
} 