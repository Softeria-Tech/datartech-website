{{-- resources/views/checkout/membership-success.blade.php --}}
<x-app-layout>
    <div class="min-h-screen bg-gray-50 py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="p-8 text-center">
                    <!-- Success Icon -->
                    <div class="mb-6">
                        <div class="inline-flex items-center justify-center h-20 w-20 rounded-full bg-green-100">
                            <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>

                    <h1 class="text-3xl font-bold text-gray-900 mb-4">
                        Welcome to {{ $subscription->name }}!
                    </h1>
                    
                    <p class="text-lg text-gray-600 mb-8">
                        Thank you for your membership purchase. Your subscription is now active.
                    </p>

                    <!-- Subscription Details -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-8 text-left">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Subscription Details</h2>
                        
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm text-gray-500">Order Number</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $order->order_number }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm text-gray-500">Plan</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $subscription->name }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm text-gray-500">Billing Cycle</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ ucfirst($subscription->plan) }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm text-gray-500">Amount Paid</dt>
                                <dd class="text-sm font-medium text-gray-900">KES {{ number_format($order->total, 2) }}</dd>
                            </div>
                            
                            @if($subscription->trial_ends_at)
                                <div>
                                    <dt class="text-sm text-gray-500">Trial Ends</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $subscription->trial_ends_at->format('M d, Y') }}</dd>
                                </div>
                            @endif
                            
                            @if($subscription->ends_at)
                                <div>
                                    <dt class="text-sm text-gray-500">Next Billing</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $subscription->ends_at->format('M d, Y') }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row justify-center space-y-3 sm:space-y-0 sm:space-x-4">
                        <a href="{{ route('dashboard') }}" 
                           class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            Manage Subscription
                        </a>
                        
                        <a href="{{ route('library.resources') }}" 
                           class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Browse Resources
                        </a>
                    </div>

                    <!-- Email Confirmation -->
                    <p class="mt-6 text-sm text-gray-500">
                        A confirmation email has been sent to {{ Auth::user()->email }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>