<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('SMS Logs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Search & Filter Form -->
                    <form action="{{ route('sms-logs.index') }}" method="GET" class="mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <!-- Search -->
                            <div>
                                <x-input-label for="search" :value="__('Search')" />
                                <x-text-input id="search" name="search" :value="request('search')" class="block mt-1 w-full" placeholder="Search in phone or message..." />
                            </div>
                            
                            <!-- Phone Number Filter -->
                            <div>
                                <x-input-label for="phone_number" :value="__('Phone Number')" />
                                <x-text-input id="phone_number" name="phone_number" :value="request('phone_number')" class="block mt-1 w-full" placeholder="Filter by phone number..." />
                            </div>
                            
                            <!-- Status Filter -->
                            <div>
                                <x-input-label for="status" :value="__('Status')" />
                                <select id="status" name="status" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">All Statuses</option>
                                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <!-- Type Filter -->
                            <div>
                                <x-input-label for="type" :value="__('Type')" />
                                <select id="type" name="type" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">All Types</option>
                                    <option value="status_update" {{ request('type') == 'status_update' ? 'selected' : '' }}>Status Update</option>
                                    <option value="manual" {{ request('type') == 'manual' ? 'selected' : '' }}>Manual</option>
                                    <option value="status_update_resend" {{ request('type') == 'status_update_resend' ? 'selected' : '' }}>Status Update (Resent)</option>
                                    <option value="manual_resend" {{ request('type') == 'manual_resend' ? 'selected' : '' }}>Manual (Resent)</option>
                                </select>
                            </div>
                            
                            <!-- Customer Filter -->
                            <div>
                                <x-input-label for="customer_id" :value="__('Customer')" />
                                <select id="customer_id" name="customer_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">All Customers</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Service Job Filter -->
                            <div>
                                <x-input-label for="service_job_id" :value="__('Service Job')" />
                                <select id="service_job_id" name="service_job_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">All Jobs</option>
                                    @foreach($serviceJobs as $job)
                                        <option value="{{ $job->id }}" {{ request('service_job_id') == $job->id ? 'selected' : '' }}>
                                            #{{ $job->job_id }} - {{ $job->device_type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <!-- Date Range -->
                            <div>
                                <x-input-label for="date_from" :value="__('Date From')" />
                                <x-text-input type="date" id="date_from" name="date_from" :value="request('date_from')" class="block mt-1 w-full" />
                            </div>
                            
                            <div>
                                <x-input-label for="date_to" :value="__('Date To')" />
                                <x-text-input type="date" id="date_to" name="date_to" :value="request('date_to')" class="block mt-1 w-full" />
                            </div>
                        </div>
                        
                        <div class="flex items-center mt-4">
                            <x-primary-button>
                                {{ __('Filter Results') }}
                            </x-primary-button>
                            
                            @if(request()->anyFilled(['search', 'phone_number', 'status', 'type', 'customer_id', 'service_job_id', 'date_from', 'date_to']))
                                <a href="{{ route('sms-logs.index') }}" class="ml-4 text-sm text-gray-600 hover:text-gray-900">
                                    {{ __('Clear Filters') }}
                                </a>
                            @endif
                        </div>
                    </form>
                    
                    <!-- SMS Logs Table -->
                    @if($logs->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Type
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Phone
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Job
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Customer
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($logs as $log)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $log->created_at->format('Y-m-d H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if(strpos($log->type, 'status_update') !== false)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                        Status
                                                    </span>
                                                @elseif(strpos($log->type, 'manual') !== false)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                        Manual
                                                    </span>
                                                @else
                                                    {{ $log->type }}
                                                @endif
                                                
                                                @if(strpos($log->type, 'resend') !== false)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Resent
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $log->phone_number }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if($log->serviceJob)
                                                    <a href="{{ route('jobs.show', $log->serviceJob) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        #{{ $log->serviceJob->job_id }}
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if($log->customer)
                                                    <a href="{{ route('customers.show', $log->customer) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        {{ $log->customer->name }}
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($log->status == 'sent')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Sent
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Failed
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('sms-logs.show', $log) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                    View
                                                </a>
                                                
                                                <form action="{{ route('sms-logs.resend', $log) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-pink-600 hover:text-pink-900" onclick="return confirm('Are you sure you want to resend this SMS?')">
                                                        Resend
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            {{ $logs->links() }}
                        </div>
                    @else
                        <div class="bg-white px-4 py-5 text-center">
                            <p class="text-gray-500">No SMS logs found matching your criteria.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 