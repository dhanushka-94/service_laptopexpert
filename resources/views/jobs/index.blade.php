<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Service Jobs') }}
            </h2>
            <a href="{{ route('jobs.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm">Create New Job</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Filters -->
                    <div class="mb-6 bg-gray-50 p-4 rounded-md">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Filter Jobs</h3>
                        <form action="{{ route('jobs.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Search -->
                            <div>
                                <label for="search" class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Job ID, Customer, Device..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm">
                            </div>
                            
                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                                <select name="status" id="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm">
                                    <option value="">All Statuses</option>
                                    @foreach(['Pending', 'In Progress', 'Awaiting Parts', 'Repaired', 'Delivered', 'Canceled'] as $status)
                                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Technician -->
                            <div>
                                <label for="technician_id" class="block text-xs font-medium text-gray-500 mb-1">Technician</label>
                                <select name="technician_id" id="technician_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm">
                                    <option value="">All Technicians</option>
                                    @foreach($technicians as $technician)
                                        <option value="{{ $technician->id }}" {{ request('technician_id') == $technician->id ? 'selected' : '' }}>{{ $technician->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Date Range (simplified) -->
                            <div>
                                <label for="date_from" class="block text-xs font-medium text-gray-500 mb-1">From Date</label>
                                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm">
                            </div>
                            
                            <div>
                                <label for="date_to" class="block text-xs font-medium text-gray-500 mb-1">To Date</label>
                                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm">
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex items-end space-x-2 md:col-span-2">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm">Apply Filters</button>
                                @if(request()->anyFilled(['search', 'status', 'technician_id', 'date_from', 'date_to']))
                                    <a href="{{ route('jobs.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm">Clear Filters</a>
                                @endif
                                
                                @if(request()->anyFilled(['status', 'technician_id', 'date_from', 'date_to']))
                                    <a href="{{ route('reports.service', request()->all()) }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md text-sm" target="_blank">Generate Report</a>
                                @endif
                            </div>
                        </form>
                    </div>
                    
                    <!-- Jobs Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Device</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Technician</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($jobs as $job)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $job->job_id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <a href="{{ route('customers.show', $job->customer) }}" class="text-blue-600 hover:text-blue-900">
                                                {{ $job->customer->name }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $job->device_type }}
                                            @if($job->brand || $job->model)
                                                <span class="block text-xs text-gray-500 mt-1">
                                                    {{ $job->brand }} {{ $job->model }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $job->technician->name ?? 'Unassigned' }}
                                        </td>
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
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                            <a href="{{ route('jobs.show', $job) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                            <a href="{{ route('jobs.edit', $job) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                            <a href="{{ route('jobs.pdf', $job) }}" class="text-green-600 hover:text-green-900" target="_blank">PDF</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No jobs found.</td>
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