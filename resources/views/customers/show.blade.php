<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Customer Details') }}
            </h2>
            <div>
                <a href="{{ route('customers.edit', $customer) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm mr-2">Edit Customer</a>
                <a href="{{ route('jobs.create') }}?customer_id={{ $customer->id }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md text-sm">Create Job</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Customer Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Customer Information</h3>
                            
                            <div class="mb-4">
                                <span class="text-sm font-medium text-gray-500 block">Name:</span>
                                <span class="text-base">{{ $customer->name }}</span>
                            </div>
                            
                            <div class="mb-4">
                                <span class="text-sm font-medium text-gray-500 block">Email:</span>
                                <span class="text-base">{{ $customer->email ?? 'Not provided' }}</span>
                            </div>
                            
                            <div class="mb-4">
                                <span class="text-sm font-medium text-gray-500 block">Phone No 1:</span>
                                <span class="text-base">{{ $customer->phone_1 ?? 'Not provided' }}</span>
                            </div>
                            
                            <div class="mb-4">
                                <span class="text-sm font-medium text-gray-500 block">Phone No 2:</span>
                                <span class="text-base">{{ $customer->phone_2 ?? 'Not provided' }}</span>
                            </div>
                            
                            <div class="mb-4">
                                <span class="text-sm font-medium text-gray-500 block">WhatsApp No:</span>
                                <span class="text-base">{{ $customer->whatsapp_no ?? 'Not provided' }}</span>
                            </div>
                            
                            <div class="mb-4">
                                <span class="text-sm font-medium text-gray-500 block">Address:</span>
                                <span class="text-base">{{ $customer->address ?? 'Not provided' }}</span>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Summary</h3>
                            
                            <div class="mb-4">
                                <span class="text-sm font-medium text-gray-500 block">Total Jobs:</span>
                                <span class="text-xl font-bold">{{ $jobs->total() }}</span>
                            </div>
                            
                            <div class="mb-4">
                                <span class="text-sm font-medium text-gray-500 block">Active Jobs:</span>
                                <span class="text-xl font-bold">{{ $customer->jobs()->whereNotIn('status', ['Delivered', 'Canceled'])->count() }}</span>
                            </div>
                            
                            <div class="mb-4">
                                <span class="text-sm font-medium text-gray-500 block">Completed Jobs:</span>
                                <span class="text-xl font-bold">{{ $customer->jobs()->whereIn('status', ['Delivered'])->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Customer Jobs -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Job History</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Device</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Issue</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($jobs as $job)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $job->job_id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $job->device_type }}
                                            @if($job->brand || $job->model)
                                                <span class="block text-xs text-gray-500 mt-1">
                                                    {{ $job->brand }} {{ $job->model }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ $job->reported_issues }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusClass = [
                                                    'Pending' => 'bg-yellow-100 text-yellow-800',
                                                    'In Progress' => 'bg-blue-100 text-blue-800',
                                                    'Awaiting Parts' => 'bg-purple-100 text-purple-800',
                                                    'Repaired' => 'bg-green-100 text-green-800',
                                                    'Delivered' => 'bg-gray-100 text-gray-800',
                                                    'Canceled' => 'bg-red-100 text-red-800',
                                                ][$job->status] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                                {{ $job->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $job->created_at->format('M d, Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('jobs.show', $job) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No jobs found for this customer.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $jobs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 