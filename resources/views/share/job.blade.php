<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job #{{ $job->job_id }} - Laptop Experts Service Center</title>
    <!-- Include Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Header -->
            <div class="bg-blue-600 px-6 py-4">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <img src="{{ asset('logo.png') }}" alt="Logo" class="h-10">
                        <h1 class="text-xl font-bold text-white ml-4">Laptop Experts Service Center</h1>
                    </div>
                    <div>
                        <a href="{{ route('share.job.pdf', $job->shareableToken->token) }}" class="px-4 py-2 bg-white text-blue-600 rounded-md hover:bg-blue-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Download PDF
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Job Info -->
            <div class="px-6 py-6 border-b">
                <div class="flex justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Job #{{ $job->job_id }}</h2>
                        <p class="text-sm text-gray-600">Created: {{ $job->created_at->format('d M Y, h:i A') }}</p>
                    </div>
                    <div>
                        <span class="px-3 py-1 rounded-full text-sm font-semibold
                            @if($job->status == 'Pending') bg-yellow-100 text-yellow-800
                            @elseif($job->status == 'In Progress') bg-blue-100 text-blue-800
                            @elseif($job->status == 'Awaiting Parts') bg-purple-100 text-purple-800
                            @elseif($job->status == 'Repaired') bg-green-100 text-green-800
                            @elseif($job->status == 'Delivered') bg-gray-100 text-gray-800
                            @elseif($job->status == 'Canceled') bg-red-100 text-red-800
                            @endif">
                            {{ $job->status }}
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="px-6 py-6 grid md:grid-cols-2 gap-6">
                <!-- Customer Info -->
                <div class="border rounded-lg p-4">
                    <h3 class="font-semibold text-lg mb-3 text-gray-700">Customer Information</h3>
                    <p><span class="font-medium">Name:</span> {{ $job->customer->name }}</p>
                    <p><span class="font-medium">Email:</span> {{ $job->customer->email }}</p>
                    @if($job->customer->phone_1)
                        <p><span class="font-medium">Phone:</span> {{ $job->customer->phone_1 }}</p>
                    @endif
                    @if($job->customer->address)
                        <p><span class="font-medium">Address:</span> {{ $job->customer->address }}</p>
                    @endif
                </div>
                
                <!-- Device Info -->
                <div class="border rounded-lg p-4">
                    <h3 class="font-semibold text-lg mb-3 text-gray-700">Device Information</h3>
                    <p><span class="font-medium">Type:</span> {{ $job->device_type }}</p>
                    @if($job->brand)
                        <p><span class="font-medium">Brand:</span> {{ $job->brand }}</p>
                    @endif
                    @if($job->model)
                        <p><span class="font-medium">Model:</span> {{ $job->model }}</p>
                    @endif
                    @if($job->serial_number)
                        <p><span class="font-medium">Serial Number:</span> {{ $job->serial_number }}</p>
                    @endif
                </div>
            </div>
            
            <!-- Reported Issues -->
            <div class="px-6 py-6 border-t">
                <h3 class="font-semibold text-lg mb-3 text-gray-700">Reported Issues</h3>
                <div class="bg-gray-50 p-4 rounded">
                    {{ $job->reported_issues }}
                </div>
            </div>
            
            <!-- Diagnostics and Repair Information (if available) -->
            @if($job->diagnosis || $job->repair_notes)
                <div class="px-6 py-6 border-t">
                    <h3 class="font-semibold text-lg mb-3 text-gray-700">Diagnostics & Repair</h3>
                    
                    @if($job->diagnosis)
                        <div class="mb-4">
                            <h4 class="font-medium text-gray-600">Diagnosis:</h4>
                            <div class="bg-gray-50 p-4 rounded mt-2">
                                {{ $job->diagnosis }}
                            </div>
                        </div>
                    @endif
                    
                    @if($job->repair_notes)
                        <div>
                            <h4 class="font-medium text-gray-600">Repair Notes:</h4>
                            <div class="bg-gray-50 p-4 rounded mt-2">
                                {{ $job->repair_notes }}
                            </div>
                        </div>
                    @endif
                </div>
            @endif
            
            <!-- Parts and Cost Information -->
            <div class="px-6 py-6 border-t">
                @if($job->parts_used)
                    <div class="mb-4">
                        <h4 class="font-medium text-gray-600">Parts Used:</h4>
                        <div class="bg-gray-50 p-4 rounded mt-2">
                            {{ $job->parts_used }}
                        </div>
                    </div>
                @endif
                
                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div>
                        <h4 class="font-medium text-gray-600">Estimated Cost:</h4>
                        <p class="text-lg font-semibold">LKR {{ number_format($job->estimated_cost ?? 0, 2) }}</p>
                    </div>
                    
                    <div>
                        <h4 class="font-medium text-gray-600">Final Cost:</h4>
                        <p class="text-lg font-semibold">LKR {{ number_format($job->final_cost ?? 0, 2) }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Job Notes -->
            @if(count($job->notes) > 0)
                <div class="px-6 py-6 border-t">
                    <h3 class="font-semibold text-lg mb-3 text-gray-700">Service Notes</h3>
                    
                    <div class="space-y-4">
                        @foreach($job->notes as $note)
                            <div class="border rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <p class="font-medium">{{ $note->user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $note->created_at->format('d M Y, h:i A') }}</p>
                                </div>
                                <div class="mt-2">
                                    {{ $note->note }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50">
                <p class="text-center text-sm text-gray-600">
                    Thank you for choosing Laptop Experts Service Center. For any inquiries, please contact us at support@laptopexperts.lk
                </p>
            </div>
        </div>
    </div>
</body>
</html> 