<?php

namespace App\Http\Controllers;

use App\Models\ServiceJob;
use App\Models\ShareableToken;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class ShareableController extends Controller
{
    /**
     * Generate a shareable link for a job.
     */
    public function generateLink(ServiceJob $job)
    {
        // Check if a token already exists and is not expired
        $existingToken = $job->shareableToken;
        
        if ($existingToken && $existingToken->expires_at && $existingToken->expires_at->isFuture()) {
            $url = URL::to('/share/' . $existingToken->token);
            return response()->json(['url' => $url]);
        }
        
        // Generate a new token
        $token = Str::random(64);
        $expiresAt = Carbon::now()->addDays(7); // Token expires in 7 days
        
        // Create or update the token
        if ($existingToken) {
            $existingToken->update([
                'token' => $token,
                'expires_at' => $expiresAt,
            ]);
        } else {
            ShareableToken::create([
                'service_job_id' => $job->id,
                'token' => $token,
                'expires_at' => $expiresAt,
            ]);
        }
        
        $url = URL::to('/share/' . $token);
        return response()->json(['url' => $url]);
    }
    
    /**
     * View a job using a shareable link.
     */
    public function viewSharedJob($token)
    {
        $shareableToken = ShareableToken::where('token', $token)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->firstOrFail();
        
        $job = $shareableToken->serviceJob;
        $job->load(['customer', 'technician', 'notes' => function($query) {
            $query->where('is_private', false)->latest();
        }]);
        
        return view('share.job', compact('job'));
    }
    
    /**
     * Download a PDF for a shared job.
     */
    public function downloadSharedJobPDF($token)
    {
        $shareableToken = ShareableToken::where('token', $token)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->firstOrFail();
        
        $job = $shareableToken->serviceJob;
        $job->load(['customer', 'technician', 'notes' => function($query) {
            $query->where('is_private', false)->latest();
        }]);
        
        $pdf = PDF::loadView('pdf.job_note', compact('job'));
        
        return $pdf->download("job-note-{$job->job_id}.pdf");
    }
} 