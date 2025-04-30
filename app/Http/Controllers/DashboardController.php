<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\ServiceJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Job count by status
        $jobsByStatus = ServiceJob::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();
            
        // Recent jobs
        $recentJobs = ServiceJob::with(['customer', 'technician'])
            ->latest()
            ->limit(5)
            ->get();
            
        // Customer count
        $customerCount = Customer::count();
        
        // Technician count
        $technicianCount = User::role('technician')->count();
        
        // Jobs requiring attention (Awaiting Parts or In Progress)
        $attentionCount = ServiceJob::whereIn('status', ['Awaiting Parts', 'In Progress'])->count();
        
        return view('dashboard', compact(
            'jobsByStatus', 
            'recentJobs', 
            'customerCount', 
            'technicianCount', 
            'attentionCount'
        ));
    }
}
