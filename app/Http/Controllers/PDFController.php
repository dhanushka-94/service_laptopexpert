<?php

namespace App\Http\Controllers;

use App\Models\ServiceJob;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PDFController extends Controller
{
    /**
     * Generate a PDF for a job.
     */
    public function generateJobPDF(ServiceJob $job)
    {
        $job->load(['customer', 'technician', 'notes' => function($query) {
            $query->where('is_private', false)->latest();
        }]);
        
        $pdf = PDF::loadView('pdf.job_note', compact('job'));
        
        return $pdf->stream("job-note-{$job->job_id}.pdf");
    }
    
    /**
     * Download a PDF for a job.
     */
    public function downloadJobPDF(ServiceJob $job)
    {
        $job->load(['customer', 'technician', 'notes' => function($query) {
            $query->where('is_private', false)->latest();
        }]);
        
        $pdf = PDF::loadView('pdf.job_note', compact('job'));
        
        return $pdf->download("job-note-{$job->job_id}.pdf");
    }
    
    /**
     * Generate a service report PDF.
     */
    public function generateServiceReport(Request $request)
    {
        $query = ServiceJob::with(['customer', 'technician']);
        
        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by technician
        if ($request->filled('technician_id')) {
            $query->where('technician_id', $request->technician_id);
        }
        
        $jobs = $query->latest()->get();
        
        $pdf = PDF::loadView('pdf.service_report', [
            'jobs' => $jobs,
            'dateFrom' => $request->date_from,
            'dateTo' => $request->date_to,
            'status' => $request->status,
        ]);
        
        return $pdf->stream('service-report.pdf');
    }

    /**
     * Display a printable receipt for the customer.
     */
    public function printJobReceipt(ServiceJob $job)
    {
        $job->load(['customer', 'technician', 'notes' => function($query) {
            $query->where('is_private', false)->latest();
        }]);
        
        return view('print.job_receipt', compact('job'));
    }
}
