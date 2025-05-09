<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobNoteController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceJobController;
use App\Http\Controllers\ShareableController;
use App\Http\Controllers\SMSController;
use App\Http\Controllers\SmsLogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Public shareable routes
Route::get('/share/{token}', [ShareableController::class, 'viewSharedJob'])->name('share.job');
Route::get('/share/{token}/pdf', [ShareableController::class, 'downloadSharedJobPDF'])->name('share.job.pdf');

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Customers
    Route::resource('customers', CustomerController::class);
    
    // Service Jobs
    Route::resource('jobs', ServiceJobController::class);
    Route::put('/jobs/{job}/status', [ServiceJobController::class, 'updateStatus'])->name('jobs.update-status');
    Route::put('/jobs/{job}/assign', [ServiceJobController::class, 'assignTechnician'])->name('jobs.assign-technician');
    
    // Job Notes
    Route::resource('notes', JobNoteController::class);
    
    // PDF Generation
    Route::get('/jobs/{job}/pdf', [PDFController::class, 'generateJobPDF'])->name('jobs.pdf');
    Route::get('/jobs/{job}/pdf/download', [PDFController::class, 'downloadJobPDF'])->name('jobs.pdf.download');
    Route::get('/jobs/{job}/print', [PDFController::class, 'printJobReceipt'])->name('jobs.print');
    Route::get('/reports/service', [PDFController::class, 'generateServiceReport'])->name('reports.service');
    
    // Shareable Links
    Route::get('/jobs/{job}/generate-link', [ShareableController::class, 'generateLink'])->name('jobs.generate-link');
    
    // SMS Notifications
    Route::post('/jobs/{job}/send-sms', [SMSController::class, 'sendJobNoteSMS'])->name('jobs.send-sms');
    
    // SMS Logs
    Route::get('/sms-logs', [SmsLogController::class, 'index'])->name('sms-logs.index');
    Route::get('/sms-logs/{smsLog}', [SmsLogController::class, 'show'])->name('sms-logs.show');
    Route::post('/sms-logs/{smsLog}/resend', [SmsLogController::class, 'resend'])->name('sms-logs.resend');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
