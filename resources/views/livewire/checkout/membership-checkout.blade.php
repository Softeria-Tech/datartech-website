{{-- resources/views/livewire/checkout/membership-checkout.blade.php --}}
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('membership.plans') }}" class="text-sm text-gray-600 hover:text-gray-900 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Plans
            </a>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Checkout Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-lg font-semibold text-gray-900">Complete Your Membership Purchase</h2>
                    </div>

                    @if($paymentStep === 'method')
                    <div class="p-6">
                        <h3 class="text-md font-medium text-gray-900 mb-4">Payment Method</h3>
                        
                        <div class="space-y-4">
                            <!-- M-PESA Option -->
                            <label class="block border rounded-lg p-4 cursor-pointer transition-colors {{ $paymentMethod === 'mpesa' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300' }}">
                                <div class="flex items-center">
                                    <input type="radio" 
                                            wire:model="paymentMethod" 
                                            value="mpesa" 
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <div class="ml-3 flex items-center">
                                        <img src="{{ asset('assets/frontend/images/mpesa-logo.png') }}" alt="M-PESA" class="h-8">
                                        <span class="ml-2 text-sm font-medium text-gray-900">M-PESA</span>
                                    </div>
                                </div>
                            </label>

                            @if($paymentMethod === 'mpesa' && $showStkPushForm)
                                <div class="ml-7 mt-4 space-y-4">
                                    <!-- Phone Number -->
                                    <div>
                                        <label for="mpesaPhone" class="block text-sm font-medium text-gray-700 mb-1">
                                            M-PESA Phone Number <span class="text-red-500">*</span>
                                        </label>
                                        <input type="tel" 
                                                wire:model="mpesaPhone" 
                                                id="mpesaPhone"
                                                placeholder="e.g., 0712345678"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <p class="mt-1 text-xs text-gray-500">
                                            Enter the phone number registered with M-PESA
                                        </p>
                                        @error('mpesaPhone') 
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Provider Selection -->
                                    <div style="display: none;">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Network Provider
                                        </label>

                                        <div class="grid grid-cols-3 gap-3">
                                            <select wire:model="mpesaProvider" 
                                                    class="w-32 px-3 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-primary-500">
                                                <option value="safaricom">Safaricom</option>
                                                <option value="airtel">Airtel</option>
                                                <option value="telkom">Telkom</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-8 flex justify-between">
                            <button type="button" 
                                    wire:click="initiateMpesaPayment"
                                    wire:loading.attr="disabled"
                                    class="px-6 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50">
                                <span wire:loading.remove wire:target="initiateMpesaPayment">
                                    Pay KES {{ number_format($order->total, 0) }}
                                </span>
                                <span wire:loading wire:target="initiateMpesaPayment">
                                    Processing...
                                </span>
                            </button>
                        </div>                        
                    </div>

                    @elseif($paymentStep === 'processing')
                        <!-- Processing Payment -->
                        <div class="p-8 text-center">
                            <div class="mb-6">
                                <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-blue-100 text-blue-600 animate-pulse">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            
                            <h3 wire:poll.5s="checkPaymentStatus" class="text-lg font-medium text-gray-900 mb-2">Waiting for Payment</h3>
                            <p class="text-gray-600 mb-4">
                                Please check your phone and enter your M-PESA PIN to complete the payment.
                            </p>
                            
                            <!-- Timer -->
                            @if($showTimer)
                                <div class="mb-6">
                                    <div class="text-3xl font-bold text-gray-900 mb-2">
                                        {{ floor($timerSeconds / 60) }}:{{ str_pad($timerSeconds % 60, 2, '0', STR_PAD_LEFT) }}
                                    </div>
                                    <p class="text-sm text-gray-500">Time remaining to complete payment</p>
                                </div>
                            @endif

                            <!-- Transaction Details -->
                            <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
                                <p class="text-sm text-gray-600 mb-2">Transaction Details:</p>
                                <p class="text-xs text-gray-500">Order: {{ $order->order_number }}</p>
                                <p class="text-xs text-gray-500">Amount: KES {{ number_format($order->total, 0) }}</p>
                                @if($transactionId)
                                    <p class="text-xs text-gray-500">Ref: {{ $transactionId }}</p>
                                @endif
                            </div>

                            <!-- Cancel Button -->
                            <button wire:click="cancelCheckout"
                                    class="mt-6 text-sm text-gray-500 hover:text-gray-700">
                                Cancel Payment
                            </button>
                        </div>

                    @elseif($paymentStep === 'success')
                        <!-- Success Message -->
                        <div class="p-8 text-center">
                            <div class="mb-6">
                                <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-green-100 text-green-600">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            </div>
                            
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Payment Successful!</h3>
                            <p class="text-gray-600 mb-4">
                                Thank you for your membership purchase. Your subscription is now active.
                            </p>
                            
                            <div class="bg-green-50 rounded-lg p-4 mb-6 text-left">
                                <p class="text-sm text-green-800 mb-2">What's next?</p>
                                <ul class="text-sm text-green-700 space-y-1">
                                    <li>✓ You now have access to {{ $package->name }} resources</li>
                                    @if($package->trial_days > 0)
                                        <li>✓ {{ $package->trial_days }}-day trial period started</li>
                                    @endif
                                    <li>✓ Download limit: {{ $package->download_limit_per_month ?? 'Unlimited' }}/month</li>
                                    <li>✓ Check your email for confirmation</li>
                                </ul>
                            </div>
                            
                            <p class="text-sm text-gray-500 mb-4">
                                Redirecting to your subscriptions page...
                            </p>
                            
                            <div class="flex justify-center space-x-4">
                                <a href="{{ route('subscriptions.index') }}" 
                                   class="px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                                    Go to My Subscriptions
                                </a>
                                <a href="{{ route('library.resources') }}" 
                                   class="px-6 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
                                    Browse Resources
                                </a>
                            </div>
                        </div>

                    @elseif($paymentStep === 'failed')
                        <!-- Failed Payment -->
                        <div class="p-8 text-center">
                            <div class="mb-6">
                                <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-red-100 text-red-600">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </div>
                            </div>
                            
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Payment Failed</h3>
                            <p class="text-red-600 mb-4">{{ $errorMessage }}</p>
                            
                            <div class="space-x-4">
                                <button wire:click="retryPayment"
                                        class="px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                                    Try Again
                                </button>
                                <a href="{{ route('membership.plans') }}" 
                                   class="px-6 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
                                    Choose Different Plan
                                </a>
                            </div>
                        </div>
                    
                    @elseif ($paymentStep === 'verify_manual')
                            <div class="text-center py-8 animate-fade-in">
                                <div class="mb-6">
                                    <div class="w-20 h-20 mx-auto bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center">
                                        <svg class="w-10 h-10 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                    Verifying Payment
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">
                                    Please wait while we verify your payment. This may take a moment.
                                </p>
                            </div>
                    @endif
                </div>

                <hr style="margin: 20px 0;" class="border-gray-200"/>
                <!-- Manual Confirmation -->
                <div class="p-6 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-200 dark:border-gray-700">
                    <summary class="text-gray-500 dark:text-gray-400 cursor-pointer hover:text-gray-700 dark:hover:text-gray-300">
                        Pay Manually
                    </summary>
                    <div class="mt-4 space-y-3">
                        <h4 class="font-medium text-gray-900 dark:text-white">DATARTECH DIGITAL SOLUTIONS</h4>
                        <ol>
                            <li>MPESA BUY GOODS</li>
                            <li>TILL NUMBER: <strong>5719198</strong></li>
                            <li>Enter Amount: <strong>{{ number_format($order->total, 0) }}</strong></li>
                        </ol>
                        <div class="flex space-x-2">
                            <textarea type="text" 
                                wire:model="transactionId"
                                placeholder="Enter M-PESA Message or Reference No."
                                class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                            <button wire:click="manualConfirmPayment"
                                    class="px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700">
                                Verify
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm overflow-hidden sticky top-4">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">Order Summary</h3>
                    </div>
                    
                    <div class="p-6">
                        <!-- Package Details -->
                        <div class="mb-6">
                            <h4 class="font-medium text-gray-900 mb-2">{{ $package->name }}</h4>
                            <p class="text-sm text-gray-600 mb-2">{{ $package->description }}</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ ucfirst($billingCycle) }} Plan
                            </span>
                        </div>

                        <!-- Key Features -->
                        <div class="mb-6">
                            <h5 class="text-sm font-medium text-gray-700 mb-3">Key Features:</h5>
                            <ul class="space-y-2">
                                @php
                                    $features = is_array($package->features) ? $package->features : json_decode($package->features, true) ?? [];
                                    $displayFeatures = array_slice($features, 0, 4);
                                @endphp
                                
                                @foreach($displayFeatures as $feature)
                                    <li class="flex items-start text-sm">
                                        <svg class="h-4 w-4 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        {{ $feature }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Download Limits -->
                        <div class="mb-6 bg-gray-50 rounded-lg p-4">
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-600">Monthly Downloads:</span>
                                <span class="font-medium">{{ $package->download_limit_per_month ?? 'Unlimited' }}</span>
                            </div>
                            @if($package->download_limit_per_day)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Daily Downloads:</span>
                                    <span class="font-medium">{{ $package->download_limit_per_day }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Price Breakdown -->
                        <div class="border-t border-gray-200 pt-4">
                            {{-- <h5 class="text-sm font-medium text-gray-700 mb-3">Price Details:</h5>
                            
                            <div class="space-y-2 mb-3">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Subtotal:</span>
                                    <span class="font-medium">KES {{ number_format($subtotal, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">VAT ({{ $taxRate }}%):</span>
                                    <span class="font-medium">KES {{ number_format($tax, 2) }}</span>
                                </div>
                            </div> --}}

                            <!-- Savings -->
                            @if($savings)
                                <div class="bg-green-50 rounded-lg p-3 mb-3">
                                    <p class="text-xs text-green-800">
                                        You save KES {{ number_format($savings['amount'], 2) }} 
                                        ({{ $savings['percentage'] }}%) compared to monthly
                                    </p>
                                </div>
                            @endif

                            <!-- Total -->
                            <div class="flex justify-between text-lg font-bold pt-3 border-t border-gray-200">
                                <span>Total:</span>
                                <span class="text-blue-600">KES {{ number_format($order->total, 0) }}</span>
                            </div>
                        </div>

                        <!-- Payment Info -->
                        <div class="mt-6 text-xs text-gray-500">
                            <p class="flex items-center mb-1">
                                <svg class="h-4 w-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                Secure payment via M-PESA
                            </p>
                            <p class="flex items-center">
                                <svg class="h-4 w-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                                {{ $package->trial_days > 0 ? $package->trial_days . '-day trial included' : 'No trial period' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for Timer -->
    <script>
        document.addEventListener('livewire:init', () => {
            let timerInterval;
            
            Livewire.on('startTimer', () => {
                if (timerInterval) clearInterval(timerInterval);
                
                timerInterval = setInterval(() => {
                    Livewire.dispatch('checkPaymentStatus');
                }, 5000);
            });
            
            Livewire.on('paymentCompleted', () => {
                if (timerInterval) clearInterval(timerInterval);
            });
        });
    </script>
</div>