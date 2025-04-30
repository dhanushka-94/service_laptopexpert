<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Service Job') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('jobs.store') }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Left Column -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Customer Information</h3>
                                
                                <!-- Customer Selection -->
                                <div class="mb-4">
                                    <label for="customer_id" class="block text-sm font-medium text-gray-700">Customer *</label>
                                    <select name="customer_id" id="customer_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <option value="">Select Customer</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ old('customer_id', request('customer_id')) == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }} {{ $customer->phone ? '('.$customer->phone.')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                    
                                    <div class="mt-2">
                                        <a href="{{ route('customers.create') }}" class="text-blue-600 hover:text-blue-900 text-sm" target="_blank">+ Add New Customer</a>
                                    </div>
                                </div>
                                
                                <!-- Technician Assignment -->
                                <div class="mb-4">
                                    <label for="technician_id" class="block text-sm font-medium text-gray-700">Assign Technician</label>
                                    <select name="technician_id" id="technician_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <option value="">Unassigned</option>
                                        @foreach($technicians as $technician)
                                            <option value="{{ $technician->id }}" {{ old('technician_id') == $technician->id ? 'selected' : '' }}>
                                                {{ $technician->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('technician_id')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- Status -->
                                <div class="mb-4">
                                    <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                                    <select name="status" id="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        @foreach(['Pending', 'In Progress', 'Awaiting Parts', 'Repaired', 'Delivered', 'Canceled'] as $status)
                                            <option value="{{ $status }}" {{ old('status', 'Pending') == $status ? 'selected' : '' }}>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Right Column -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Device Information</h3>
                                
                                <!-- Device Type -->
                                <div class="mb-4">
                                    <label for="device_type" class="block text-sm font-medium text-gray-700">Device Type *</label>
                                    <select name="device_type" id="device_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <option value="">Select Type</option>
                                        <option value="Laptop" {{ old('device_type') == 'Laptop' ? 'selected' : '' }}>Laptop</option>
                                        <option value="Desktop" {{ old('device_type') == 'Desktop' ? 'selected' : '' }}>Desktop</option>
                                        <option value="Other" {{ old('device_type') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('device_type')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- Brand -->
                                <div class="mb-4">
                                    <label for="brand" class="block text-sm font-medium text-gray-700">Brand</label>
                                    <input type="text" name="brand" id="brand" value="{{ old('brand') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    @error('brand')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- Model -->
                                <div class="mb-4">
                                    <label for="model" class="block text-sm font-medium text-gray-700">Model</label>
                                    <input type="text" name="model" id="model" value="{{ old('model') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    @error('model')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- Serial Number -->
                                <div class="mb-4">
                                    <label for="serial_number" class="block text-sm font-medium text-gray-700">Serial Number</label>
                                    <input type="text" name="serial_number" id="serial_number" value="{{ old('serial_number') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    @error('serial_number')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Common Fields -->
                        <div class="mt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Service Information</h3>
                            
                            <!-- Reported Issues -->
                            <div class="mb-4">
                                <label for="reported_issues" class="block text-sm font-medium text-gray-700">Reported Issues *</label>
                                <textarea name="reported_issues" id="reported_issues" rows="4" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">{{ old('reported_issues') }}</textarea>
                                @error('reported_issues')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Accessories -->
                            <div class="mb-4">
                                <label for="accessories" class="block text-sm font-medium text-gray-700">Accessories Received</label>
                                <textarea name="accessories" id="accessories" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">{{ old('accessories') }}</textarea>
                                @error('accessories')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Initial Diagnosis -->
                            <div class="mb-4">
                                <label for="diagnosis" class="block text-sm font-medium text-gray-700">Initial Diagnosis (optional)</label>
                                <textarea name="diagnosis" id="diagnosis" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">{{ old('diagnosis') }}</textarea>
                                @error('diagnosis')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Cost Estimates -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="estimated_cost" class="block text-sm font-medium text-gray-700">Estimated Cost</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">LKR</span>
                                        </div>
                                        <input type="number" step="0.01" min="0" name="estimated_cost" id="estimated_cost" value="{{ old('estimated_cost') }}" class="pl-14 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    </div>
                                    @error('estimated_cost')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="flex items-center justify-between mt-6">
                            <div class="flex items-center">
                                <input type="checkbox" name="print_receipt" id="print_receipt" value="1" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <label for="print_receipt" class="ml-2 text-sm text-gray-700">Print customer receipt after creating job</label>
                            </div>
                            
                            <div>
                                <a href="{{ route('jobs.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">Create Job</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 