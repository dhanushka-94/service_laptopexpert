<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm mb-1">Total Customers</div>
                    <div class="text-3xl font-bold">{{ $customerCount }}</div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm mb-1">Total Technicians</div>
                    <div class="text-3xl font-bold">{{ $technicianCount }}</div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm mb-1">Jobs Requiring Attention</div>
                    <div class="text-3xl font-bold">{{ $attentionCount }}</div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm mb-1">Quick Actions</div>
                    <div class="mt-2 space-y-2">
                        <a href="{{ route('jobs.create') }}" class="inline-block bg-blue-500 hover:bg-blue-600 text-white text-sm px-3 py-1 rounded">New Job</a>
                        <a href="{{ route('customers.create') }}" class="inline-block bg-green-500 hover:bg-green-600 text-white text-sm px-3 py-1 rounded ml-2">New Customer</a>
                    </div>
                </div>
            </div>
            
            <!-- Job Status Chart -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-8">
                <h3 class="text-lg font-medium mb-4">Job Status Overview</h3>
                <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                    @php
                        $statusColors = [
                            'Pending' => 'bg-yellow-500',
                            'In Progress' => 'bg-blue-500',
                            'Awaiting Parts' => 'bg-purple-500',
                            'Repaired' => 'bg-green-500',
                            'Delivered' => 'bg-gray-500',
                            'Canceled' => 'bg-red-500',
                        ];
                    @endphp
                    
                    @foreach(['Pending', 'In Progress', 'Awaiting Parts', 'Repaired', 'Delivered', 'Canceled'] as $status)
                        @php
                            $statusData = $jobsByStatus->firstWhere('status', $status);
                            $count = $statusData ? $statusData->count : 0;
                        @endphp
                        <div class="text-center">
                            <div class="text-sm text-gray-600">{{ $status }}</div>
                            <div class="text-2xl font-bold mb-2">{{ $count }}</div>
                            <div class="w-full h-2 rounded-full bg-gray-200">
                                <div class="{{ $statusColors[$status] }} h-2 rounded-full" style="width: {{ $count ? '100%' : '0%' }}"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Recent Jobs -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium mb-4">Recent Jobs</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Device</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($recentJobs as $job)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $job->job_id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $job->customer->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $job->device_type }}</td>
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
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No recent jobs found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4 text-right">
                        <a href="{{ route('jobs.index') }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">View All Jobs →</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
