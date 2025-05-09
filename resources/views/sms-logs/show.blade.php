<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('SMS Log Details') }}
            </h2>
            
            <span>
                <a href="{{ route('sms-logs.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded inline-flex items-center text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                    </svg>
                    {{ __('Back to Logs') }}
                </a>
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">SMS Information</h3>
                            
                            <div>
                                <form action="{{ route('sms-logs.resend', $smsLog) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-pink-500 hover:bg-pink-600 text-white py-2 px-4 rounded text-sm" onclick="return confirm('Are you sure you want to resend this SMS?')">
                                        Resend SMS
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-gray-500">Date Sent</h4>
                                    <p class="mt-1">{{ $smsLog->created_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                                
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-gray-500">Phone Number</h4>
                                    <p class="mt-1">{{ $smsLog->phone_number }}</p>
                                </div>
                                
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-gray-500">Status</h4>
                                    <p class="mt-1">
                                        @if($smsLog->status == 'sent')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Sent Successfully
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Failed
                                            </span>
                                        @endif
                                    </p>
                                </div>
                                
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-gray-500">Type</h4>
                                    <p class="mt-1">
                                        @if(strpos($smsLog->type, 'status_update') !== false)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Status Update
                                            </span>
                                        @elseif(strpos($smsLog->type, 'manual') !== false)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                Manual
                                            </span>
                                        @else
                                            {{ $smsLog->type }}
                                        @endif
                                        
                                        @if(strpos($smsLog->type, 'resend') !== false)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 ml-1">
                                                Resent
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            
                            <div>
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-gray-500">Service Job</h4>
                                    <p class="mt-1">
                                        @if($smsLog->serviceJob)
                                            <a href="{{ route('jobs.show', $smsLog->serviceJob) }}" class="text-indigo-600 hover:text-indigo-900">
                                                #{{ $smsLog->serviceJob->job_id }} - {{ $smsLog->serviceJob->device_type }} 
                                                {{ $smsLog->serviceJob->brand }} {{ $smsLog->serviceJob->model }}
                                            </a>
                                        @else
                                            <span class="text-gray-400">Not associated with a job</span>
                                        @endif
                                    </p>
                                </div>
                                
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-gray-500">Customer</h4>
                                    <p class="mt-1">
                                        @if($smsLog->customer)
                                            <a href="{{ route('customers.show', $smsLog->customer) }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ $smsLog->customer->name }}
                                            </a>
                                        @else
                                            <span class="text-gray-400">Not associated with a customer</span>
                                        @endif
                                    </p>
                                </div>
                                
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-gray-500">Triggered By</h4>
                                    <p class="mt-1">
                                        @if($smsLog->triggered_by == 'system')
                                            System (Automatic)
                                        @elseif($smsLog->triggeredByUser)
                                            {{ $smsLog->triggeredByUser->name }}
                                        @else
                                            Unknown
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Message Content</h3>
                        <div class="bg-gray-50 p-4 rounded-md mb-6">
                            <pre class="whitespace-pre-wrap text-sm">{{ $smsLog->message }}</pre>
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">API Response</h3>
                        <div class="bg-gray-50 p-4 rounded-md overflow-auto">
                            <pre class="text-sm">{{ $smsLog->response }}</pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 