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
                <button id="shareButton" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md text-sm">Share Link</button>
                <button id="smsButton" class="btn bg-teal-500 !bg-teal-500 hover:bg-teal-600 text-white px-4 py-2 rounded-md text-sm">Send SMS</button>
            </div>
        </div>
    </x-slot>

    <!-- Share Link Modal -->
    <div id="shareModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full mx-4 sm:mx-auto sm:max-w-lg md:max-w-xl">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Share Job Details</h3>
                <button id="closeShareModal" class="text-gray-500 hover:text-gray-700">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-2">Share this link with the customer to view job details:</p>
                <div class="flex">
                    <input id="shareLink" type="text" class="flex-1 border-gray-300 rounded-l-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" readonly>
                    <button id="copyButton" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-r-md text-sm">Copy</button>
                </div>
                <p id="copyMessage" class="text-green-600 text-sm mt-1 hidden">Link copied!</p>
            </div>
            <div class="border-t border-gray-200 pt-4">
                <h4 class="font-medium text-sm mb-2">Share via:</h4>
                <div class="flex space-x-2">
                    <a id="whatsappShare" href="#" target="_blank" class="flex-1 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md text-sm text-center">
                        WhatsApp
                    </a>
                    <a id="emailShare" href="#" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm text-center">
                        Email
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- SMS Modal -->
    <div id="smsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full mx-4 sm:mx-auto sm:max-w-lg md:max-w-xl">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Send Job Update via SMS</h3>
                <button id="closeSmsModal" class="text-gray-500 hover:text-gray-700">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <form action="{{ route('jobs.send-sms', $job) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="text" name="phone_number" id="phone_number" 
                           value="{{ $job->customer->phone_1 ?? '' }}" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <p class="text-xs text-gray-500 mt-1">Enter phone number in international format (9471XXXXXXX)</p>
                </div>
                
                <div class="mb-4">
                    <p class="text-sm font-medium text-gray-700 mb-1">Message Preview</p>
                    <div class="p-3 bg-gray-50 rounded-md text-gray-800 text-sm">
                        <p>Job #{{ $job->job_id }} Status: {{ $job->status }}</p>
                        <p>{{ $job->device_type }} {{ $job->brand }} {{ $job->model }}</p>
                        @if($job->notes->count() > 0)
                            <p>Latest update: {{ Str::limit($job->notes->first()->note, 50) }}</p>
                        @endif
                        @if($job->final_cost)
                            <p>Cost: LKR {{ number_format($job->final_cost, 2) }}</p>
                        @endif
                        <p>Thank you for choosing Laptop Experts Service Center.</p>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">The actual SMS will include a shareable link.</p>
                </div>
                
                <div class="flex justify-end">
                    <button type="button" id="cancelSmsBtn" class="text-gray-600 hover:text-gray-900 mr-2">Cancel</button>
                    <button type="submit" class="btn bg-teal-500 !bg-teal-500 hover:bg-teal-600 text-white px-4 py-2 rounded-md text-sm">Send SMS</button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif
            
            @if($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
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
                                @if($job->customer->phone_1)
                                    <span class="inline-flex items-center mt-2 text-xs text-gray-600">
                                        <svg class="w-4 h-4 mr-1 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        Status updates will be sent via SMS to {{ $job->customer->phone_1 }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center mt-2 text-xs text-yellow-600">
                                        <svg class="w-4 h-4 mr-1 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        No phone number available to send status updates
                                    </span>
                                @endif
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
        document.addEventListener('DOMContentLoaded', function() {
            // Share Link Modal
            const shareButton = document.getElementById('shareButton');
            const shareModal = document.getElementById('shareModal');
            const closeShareModal = document.getElementById('closeShareModal');
            const shareLinkInput = document.getElementById('shareLink');
            const copyButton = document.getElementById('copyButton');
            const copyMessage = document.getElementById('copyMessage');
            const whatsappShare = document.getElementById('whatsappShare');
            const emailShare = document.getElementById('emailShare');
            
            // SMS Modal
            const smsButton = document.getElementById('smsButton');
            const smsModal = document.getElementById('smsModal');
            const closeSmsModal = document.getElementById('closeSmsModal');
            const cancelSmsBtn = document.getElementById('cancelSmsBtn');
            
            // Show share modal when share button is clicked
            shareButton.addEventListener('click', function() {
                // Generate link via AJAX
                fetch('{{ route('jobs.generate-link', $job) }}')
                    .then(response => response.json())
                    .then(data => {
                        shareLinkInput.value = data.url;
                        
                        // Update share buttons
                        whatsappShare.href = `https://wa.me/?text=View your laptop service job details: ${encodeURIComponent(data.url)}`;
                        
                        const emailSubject = 'Your Laptop Service Job Details';
                        const emailBody = `Hello,\n\nYou can view your laptop service job details here: ${data.url}\n\nRegards,\nLaptop Experts Service Center`;
                        emailShare.href = `mailto:${encodeURIComponent('{{ $job->customer->email }}')}?subject=${encodeURIComponent(emailSubject)}&body=${encodeURIComponent(emailBody)}`;
                        
                        // Show modal
                        shareModal.classList.remove('hidden');
                    })
                    .catch(error => {
                        console.error('Error generating share link:', error);
                    });
            });
            
            // Close share modal
            closeShareModal.addEventListener('click', function() {
                shareModal.classList.add('hidden');
                copyMessage.classList.add('hidden');
            });
            
            // Close share modal when clicking outside
            shareModal.addEventListener('click', function(e) {
                if (e.target === shareModal) {
                    shareModal.classList.add('hidden');
                    copyMessage.classList.add('hidden');
                }
            });
            
            // Copy link to clipboard
            copyButton.addEventListener('click', function() {
                shareLinkInput.select();
                document.execCommand('copy');
                
                copyMessage.classList.remove('hidden');
                setTimeout(function() {
                    copyMessage.classList.add('hidden');
                }, 3000);
            });
            
            // Show SMS modal
            smsButton.addEventListener('click', function() {
                smsModal.classList.remove('hidden');
            });
            
            // Close SMS modal
            closeSmsModal.addEventListener('click', function() {
                smsModal.classList.add('hidden');
            });
            
            cancelSmsBtn.addEventListener('click', function() {
                smsModal.classList.add('hidden');
            });
            
            // Close SMS modal when clicking outside
            smsModal.addEventListener('click', function(e) {
                if (e.target === smsModal) {
                    smsModal.classList.add('hidden');
                }
            });
        });
    </script>
</x-app-layout> 