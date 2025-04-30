<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobNoteController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceJobController;
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
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
