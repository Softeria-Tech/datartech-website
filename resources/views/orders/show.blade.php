{{-- resources/views/orders/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Order Details') }} - {{ $order->order_number }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('orders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Orders
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Order Status Alert with Payment Action -->
            @if($order->payment_status === 'pending' || $order->payment_status === 'pending_verification')
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    <span class="font-medium">Payment pending!</span> 
                                    @if($order->payment_method === 'bank_transfer')
                                        Please complete the bank transfer to process your order.
                                    @else
                                        Your payment hasn't been completed yet.
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <!-- Complete Payment Button -->
                        <div class="flex-shrink-0">
                            @if($order->resource)
                                <!-- Resource Order -->
                                <a href="{{ route('checkout.resume', $order->order_number) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Complete Payment
                                </a>
                            @elseif(isset($order->order_data['package_id']))
                                <!-- Membership Order -->
                                <a href="{{ route('checkout.membership.resume', $order->order_number) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Complete Membership Payment
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Rest of your existing order details view -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Order Details -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Order Information</h3>
                            
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div>
                                    <p class="text-sm text-gray-500">Order Number</p>
                                    <p class="font-medium">{{ $order->order_number }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Order Date</p>
                                    <p class="font-medium">{{ $order->created_at->format('F d, Y h:i A') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Payment Status</p>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($order->payment_status === 'paid') bg-green-100 text-green-800
                                        @elseif($order->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->payment_status === 'failed') bg-red-100 text-red-800
                                        @elseif($order->payment_status === 'pending_verification') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucwords(str_replace(['_','-'], ' ', $order->payment_status)) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Order Status</p>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($order->order_status === 'completed') bg-green-100 text-green-800
                                        @elseif($order->order_status === 'processing') bg-blue-100 text-blue-800
                                        @elseif($order->order_status === 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($order->order_status) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Payment Details -->
                            @if($order->paid_at)
                                <div class="border-t border-gray-200 pt-4 mb-4">
                                    <h4 class="font-medium mb-2">Payment Details</h4>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm text-gray-500">Paid At</p>
                                            <p class="font-medium">{{ $order->paid_at->format('F d, Y h:i A') }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Payment Method</p>
                                            <p class="font-medium">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                                        </div>
                                        @if($order->reference)
                                        <div class="col-span-2">
                                            <p class="text-sm text-gray-500">Reference</p>
                                            <p class="font-medium">{{ $order->reference }}</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Purchased Items -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Purchased Items</h3>
                            
                            @if($order->resource)
                                <!-- Resource Order -->
                                <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg">
                                    @if($order->resource->thumbnail)
                                        <img src="{{ asset('storage/' . $order->resource->thumbnail) }}" 
                                            alt="{{ $order->resource->name }}"
                                            class="w-20 h-20 object-cover rounded">
                                    @else
                                        <div class="w-20 h-20 bg-gray-200 rounded flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    
                                    <div class="flex-1">
                                        <h4 class="font-medium text-lg">{{ $order->resource->name }}</h4>
                                        <p class="text-gray-600 text-sm mt-1">{!! $order->resource->description !!}</p>
                                        
                                        <div class="mt-3 flex items-center space-x-4">
                                            <span class="text-sm text-gray-500">Quantity: {{ $order->total_items }}</span>
                                            <span class="text-sm text-gray-500">Price: Ksh{{ number_format($order->subtotal, 0) }}</span>
                                        </div>
                                    </div>
                                    
                                    @if($order->payment_status === 'paid')
                                        <a href="{{ route('library.resource.detail', $order->resource->slug) }}" 
                                           class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                            </svg>
                                            Download Now
                                        </a>
                                    @endif
                                </div>
                            @elseif(isset($order->order_data['package_id']))
                                <!-- Membership Order -->
                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center mb-4">
                                        <div class="bg-blue-100 rounded-lg p-3">
                                            <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <h4 class="font-medium text-lg">{{ $order->order_data['package_name'] ?? 'Membership Package' }}</h4>
                                            <p class="text-sm text-gray-600">Billing Cycle: {{ ucfirst($order->order_data['billing_cycle'] ?? 'monthly') }}</p>
                                        </div>
                                    </div>
                                    
                                    @if($order->payment_status === 'paid' && isset($order->order_data['subscription_id']))
                                        <a href="{{ route('subscriptions.index') }}" 
                                           class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                            </svg>
                                            View Subscription
                                        </a>
                                    @endif
                                </div>
                            @else
                                <p class="text-gray-500">No items found for this order.</p>
                            @endif

                            <!-- Order Summary -->
                            <div class="mt-6 border-t border-gray-200 pt-4">
                                <h4 class="font-medium mb-2">Order Summary</h4>
                                <div class="space-y-2">
                                    {{-- <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Subtotal:</span>
                                        <span class="font-medium">Ksh{{ number_format($order->subtotal, 0) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Tax:</span>
                                        <span class="font-medium">Ksh{{ number_format($order->tax, 0) }}</span>
                                    </div> --}}
                                    <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-2 mt-2">
                                        <span>Total:</span>
                                        <span>Ksh{{ number_format($order->total, 0) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <!-- Payment Information -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Payment Information</h3>
                            
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-gray-500">Payment Method</p>
                                    <p class="font-medium">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                                </div>
                                
                                @if($order->reference)
                                <div>
                                    <p class="text-sm text-gray-500">Reference</p>
                                    <p class="font-medium text-xs break-all">{{ $order->reference }}</p>
                                </div>
                                @endif
                                
                                @if($order->payment_method === 'bank_transfer' && $order->payment_status === 'pending')
                                    <div class="mt-4 p-3 bg-blue-50 rounded-md">
                                        <p class="text-sm text-blue-800">
                                            <strong>Bank Transfer Instructions:</strong><br>
                                            Please transfer the exact amount to:<br>
                                            Bank: Example Bank<br>
                                            Account: 1234-5678-9012-3456<br>
                                            Name: Your Company Name<br>
                                            Reference: {{ $order->order_number }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Order Timeline -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Order Timeline</h3>
                            
                            <div class="flow-root">
                                <ul class="-mb-8">
                                    <li>
                                        <div class="relative pb-8">
                                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                        <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="min-w-0 flex-1 pt-1.5">
                                                    <p class="text-sm text-gray-500">Order Placed</p>
                                                    <p class="text-sm font-medium text-gray-900">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    
                                    @if($order->paid_at)
                                    <li>
                                        <div class="relative pb-8">
                                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                        <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="min-w-0 flex-1 pt-1.5">
                                                    <p class="text-sm text-gray-500">Payment Received</p>
                                                    <p class="text-sm font-medium text-gray-900">{{ $order->paid_at->format('M d, Y h:i A') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    @endif
                                    @if($order->order_status === 'processing' && $order->payment_status === 'pending_verification')
                                    <li>
                                        <div class="relative pb-8">
                                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full bg-yellow-500 flex items-center justify-center ring-8 ring-white">
                                                        <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="min-w-0 flex-1 pt-1.5">
                                                    <p class="text-sm text-gray-500">Verification</p>
                                                    <p class="text-sm font-medium text-gray-900">{{ $order->updated_at->format('M d, Y h:i A') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    @endif
                                    @if($order->order_status === 'completed')
                                    <li>
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                    <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812z"></path>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5">
                                                <p class="text-sm text-gray-500">Order Completed</p>
                                                <p class="text-sm font-medium text-gray-900">{{ $order->updated_at->format('M d, Y h:i A') }}</p>
                                            </div>
                                        </div>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>