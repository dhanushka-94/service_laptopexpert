<?php

namespace App\Http\Controllers;

use App\Http\Requests\JobNoteRequest;
use App\Models\JobNote;
use App\Models\ServiceJob;
use Illuminate\Http\Request;

class JobNoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ServiceJob $job)
    {
        $notes = $job->notes()->with('user')->latest()->get();
        
        return view('job_notes.index', compact('job', 'notes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(ServiceJob $job)
    {
        return view('job_notes.create', compact('job'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(JobNoteRequest $request)
    {
        $note = new JobNote($request->validated());
        $note->user_id = auth()->id();
        $note->save();
        
        return redirect()->route('jobs.show', $note->service_job_id)
            ->with('success', 'Note added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(JobNote $note)
    {
        return view('job_notes.show', compact('note'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JobNote $note)
    {
        return view('job_notes.edit', compact('note'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(JobNoteRequest $request, JobNote $note)
    {
        $note->update($request->validated());
        
        return redirect()->route('jobs.show', $note->service_job_id)
            ->with('success', 'Note updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobNote $note)
    {
        $jobId = $note->service_job_id;
        $note->delete();
        
        return redirect()->route('jobs.show', $jobId)
            ->with('success', 'Note deleted successfully.');
    }
}
