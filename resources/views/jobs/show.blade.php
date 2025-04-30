<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Job: {{ $job->job_id }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('jobs.edit', $job) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm">Edit Job</a>
                <a href="{{ route('jobs.print', $job) }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md text-sm" target="_blank">Print Receipt</a>
                <a href="{{ route('jobs.pdf', $job) }}" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-md text-sm" target="_blank">Generate PDF</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Job Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Job Information</h3>
                            <p class="text-sm text-gray-500">Created on {{ $job->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        
                        <div>
                            @php
                                $statusClass = [
                                    'Pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                    'In Progress' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'Awaiting Parts' => 'bg-purple-100 text-purple-800 border-purple-200',
                                    'Repaired' => 'bg-green-100 text-green-800 border-green-200',
                                    'Delivered' => 'bg-gray-100 text-gray-800 border-gray-200',
                                    'Canceled' => 'bg-red-100 text-red-800 border-red-200',
                                ][$job->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                            @endphp
                            
                            <span class="px-3 py-1 border rounded-full text-sm font-medium {{ $statusClass }}">
                                {{ $job->status }}
                            </span>
                            
                            <!-- Update Status Form -->
                            <form action="{{ route('jobs.update-status', $job) }}" method="POST" class="mt-2">
                                @csrf
                                @method('PUT')
                                <div class="flex">
                                    <select name="status" class="rounded-l-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm">
                                        @foreach(['Pending', 'In Progress', 'Awaiting Parts', 'Repaired', 'Delivered', 'Canceled'] as $status)
                                            <option value="{{ $status }}" {{ $job->status == $status ? 'selected' : '' }}>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="bg-gray-800 text-white rounded-r-md px-3 py-0 text-sm">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Customer Info -->
                        <div>
                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-500">Customer</h4>
                                <p class="text-base">
                                    <a href="{{ route('customers.show', $job->customer) }}" class="text-blue-600 hover:text-blue-900">
                                        {{ $job->customer->name }}
                                    </a>
                                </p>
                                @if($job->customer->phone)
                                    <p class="text-sm text-gray-500">Phone: {{ $job->customer->phone }}</p>
                                @endif
                                @if($job->customer->email)
                                    <p class="text-sm text-gray-500">Email: {{ $job->customer->email }}</p>
                                @endif
                            </div>
                            
                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-500">Technician</h4>
                                @if($job->technician)
                                    <p class="text-base">{{ $job->technician->name }}</p>
                                @else
                                    <p class="text-base text-gray-500">Not assigned</p>
                                    
                                    <!-- Assign Technician Form -->
                                    <form action="{{ route('jobs.assign-technician', $job) }}" method="POST" class="mt-2">
                                        @csrf
                                        @method('PUT')
                                        <div class="flex">
                                            <select name="technician_id" class="rounded-l-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm">
                                                <option value="">Select Technician</option>
                                                @foreach($technicians as $technician)
                                                    <option value="{{ $technician->id }}">{{ $technician->name }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="bg-gray-800 text-white rounded-r-md px-3 py-0 text-sm">Assign</button>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Device Info -->
                        <div>
                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-500">Device Information</h4>
                                <p class="text-base">{{ $job->device_type }}</p>
                                @if($job->brand || $job->model)
                                    <p class="text-sm text-gray-600">{{ $job->brand }} {{ $job->model }}</p>
                                @endif
                                @if($job->serial_number)
                                    <p class="text-sm text-gray-600">S/N: {{ $job->serial_number }}</p>
                                @endif
                            </div>
                            
                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-500">Accessories</h4>
                                <p class="text-base">{{ $job->accessories ?: 'None' }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-500">Cost Information</h4>
                                <p class="text-sm text-gray-600">Estimated: LKR {{ number_format($job->estimated_cost ?? 0, 2) }}</p>
                                <p class="text-sm text-gray-600">Final: LKR {{ number_format($job->final_cost ?? 0, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Service Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Service Details</h3>
                    
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Reported Issues</h4>
                        <div class="p-3 bg-gray-50 rounded-md text-gray-800">
                            {{ $job->reported_issues }}
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Diagnosis</h4>
                        <div class="p-3 bg-gray-50 rounded-md text-gray-800">
                            {{ $job->diagnosis ?: 'No diagnosis has been recorded yet.' }}
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Repair Notes</h4>
                        <div class="p-3 bg-gray-50 rounded-md text-gray-800">
                            {{ $job->repair_notes ?: 'No repair notes have been recorded yet.' }}
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Parts Used</h4>
                        <div class="p-3 bg-gray-50 rounded-md text-gray-800">
                            {{ $job->parts_used ?: 'No parts have been recorded.' }}
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Job Notes -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Job Notes</h3>
                        <button type="button" id="addNoteBtn" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm">Add Note</button>
                    </div>
                    
                    <!-- Add Note Form (hidden by default) -->
                    <div id="addNoteForm" class="mb-6 hidden">
                        <form action="{{ route('notes.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="service_job_id" value="{{ $job->id }}">
                            
                            <div class="mb-4">
                                <label for="note" class="block text-sm font-medium text-gray-700">Note Content *</label>
                                <textarea name="note" id="note" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"></textarea>
                            </div>
                            
                            <div class="mb-4">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="is_private" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-600">Private note (only visible to staff)</span>
                                </label>
                            </div>
                            
                            <div class="flex justify-end">
                                <button type="button" id="cancelNoteBtn" class="text-gray-600 hover:text-gray-900 mr-2">Cancel</button>
                                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm">Save Note</button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Notes List -->
                    <div class="space-y-4">
                        @forelse($job->notes as $note)
                            <div class="p-4 bg-gray-50 rounded-md {{ $note->is_private ? 'border-l-4 border-red-400' : '' }}">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-sm font-medium">
                                            {{ $note->user->name }}
                                            @if($note->is_private)
                                                <span class="text-xs text-red-500 ml-2">(Private)</span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $note->created_at->format('M d, Y h:i A') }}</p>
                                    </div>
                                    
                                    <div>
                                        <form action="{{ route('notes.destroy', $note) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-xs" onclick="return confirm('Are you sure you want to delete this note?')">Delete</button>
                                        </form>
                                    </div>
                                </div>
                                
                                <div class="mt-2 text-sm text-gray-800">
                                    {{ $note->note }}
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">No notes have been added yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Simple JavaScript to toggle the note form
        document.addEventListener('DOMContentLoaded', function() {
            const addNoteBtn = document.getElementById('addNoteBtn');
            const addNoteForm = document.getElementById('addNoteForm');
            const cancelNoteBtn = document.getElementById('cancelNoteBtn');
            
            addNoteBtn.addEventListener('click', function() {
                addNoteForm.classList.remove('hidden');
                addNoteBtn.classList.add('hidden');
            });
            
            cancelNoteBtn.addEventListener('click', function() {
                addNoteForm.classList.add('hidden');
                addNoteBtn.classList.remove('hidden');
            });
        });
    </script>
</x-app-layout> 