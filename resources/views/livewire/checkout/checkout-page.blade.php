{{-- resources/views/livewire/checkout/checkout-page.blade.php --}}
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="text-center mb-12">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                Complete Your Purchase
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-400">
                Order #{{ $order->order_number }}
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- Main Checkout Area --}}
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    
                    {{-- Order Summary Header --}}
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Order Summary
                        </h2>
                    </div>

                    {{-- Resource Details --}}
                    <div class="p-6">
                        <div class="flex items-start gap-4">
                            {{-- Resource Thumbnail --}}
                            <div class="w-24 h-24 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 flex-shrink-0">
                                @if($order->resource->thumbnail)
                                    <img src="{{ Storage::url($order->resource->thumbnail) }}" 
                                         alt="{{ $order->resource->title }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            {{-- Resource Info --}}
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                                    {{ $order->resource->title }}
                                </h3>
                                @if($order->resource->author)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                        By {{ $order->resource->author }}
                                    </p>
                                @endif
                                <div class="flex items-center gap-4 text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">
                                        Quantity: {{ $order->total_items }}
                                    </span>
                                    <span class="text-gray-300 dark:text-gray-600">|</span>
                                    <span class="text-gray-500 dark:text-gray-400">
                                        Unit Price: Ksh {{ number_format($order->subtotal / $order->total_items, 0) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Price Breakdown --}}
                    <div class="p-6 bg-gray-50 dark:bg-gray-700/50 border-t border-b border-gray-200 dark:border-gray-700">
                        <div class="space-y-3 max-w-md mx-auto">
                            {{-- <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                                <span class="text-gray-900 dark:text-white font-medium">
                                    Ksh{{ number_format($order->subtotal, 0) }}
                                </span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Tax</span>
                                <span class="text-gray-900 dark:text-white font-medium">
                                    Ksh{{ number_format($order->tax, 0) }}
                                </span>
                            </div> --}}
                            <div class="flex justify-between text-base font-semibold pt-3 border-t border-gray-200 dark:border-gray-600">
                                <span class="text-gray-900 dark:text-white">Total</span>
                                <span class="text-2xl text-primary-600 dark:text-primary-400">
                                    Ksh {{ number_format($order->total, 0) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Payment Methods --}}
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            Payment Method
                        </h3>

                        {{-- Payment Step: Method Selection --}}
                        @if($paymentStep === 'method')
                            <div class="space-y-4">
                                {{-- M-PESA Option --}}
                                <label class="block cursor-pointer">
                                    <input type="radio" 
                                           wire:model="paymentMethod" 
                                           value="mpesa" 
                                           class="sr-only peer">
                                    <div class="p-4 border-2 rounded-xl {{ $paymentMethod === 'mpesa' ? 'border-primary-600 bg-primary-50 dark:bg-primary-900/20' : 'border-gray-200 dark:border-gray-700' }} peer-checked:border-primary-600 peer-checked:bg-primary-50">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center">
                                                <svg class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <h4 class="font-medium text-gray-900 dark:text-white">M-PESA</h4>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    Pay instantly via M-PESA STK Push
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </label>

                                {{-- M-PESA Phone Input --}}
                                @if($paymentMethod === 'mpesa' && $showStkPushForm)
                                    <div class="mt-6 space-y-4 animate-fade-in">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                M-PESA Phone Number
                                            </label>
                                            <div class="flex gap-2">
                                                <select wire:model="mpesaProvider" style="display: none;" 
                                                        class="w-32 px-3 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-primary-500">
                                                    <option value="safaricom">Safaricom</option>
                                                    <option value="airtel">Airtel</option>
                                                    <option value="telkom">Telkom</option>
                                                </select>
                                                <input type="tel" 
                                                       wire:model="mpesaPhone" 
                                                       placeholder="0712345678"
                                                       class="flex-1 px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                                       wire:keydown.enter="initiateMpesaPayment">
                                            </div>
                                            @error('mpesaPhone') 
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                                You'll receive an STK Push prompt on this number
                                            </p>
                                        </div>

                                        <button wire:click="initiateMpesaPayment" 
                                                wire:loading.attr="disabled"
                                                class="w-full px-6 py-4 bg-primary-600 hover:bg-primary-700 disabled:bg-primary-400 text-white font-semibold rounded-xl transition flex items-center justify-center gap-2">
                                            <span wire:loading.remove>
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                            </span>
                                            <span wire:loading.remove>Pay KES {{ number_format($order->total, 0) }}</span>
                                            <span wire:loading>
                                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </span>
                                        </button>
                                    </div>
                                @endif
                            </div>

                        {{-- Payment Step: Processing --}}
                        @elseif($paymentStep === 'processing')
                            <div class="text-center py-8 animate-fade-in">
                                <div class="mb-6">
                                    <div class="w-20 h-20 mx-auto bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center">
                                        <svg class="w-10 h-10 text-primary-600 dark:text-primary-400 animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                                
                                <h3 wire:poll.5s="checkPaymentStatus" class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                    Waiting for Payment
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">
                                    Please check your phone and enter your M-PESA PIN to complete the payment
                                </p>

                                @if($showTimer)
                                    <div class="max-w-xs mx-auto bg-gray-100 dark:bg-gray-700 rounded-full h-2 mb-4">
                                        <div class="bg-primary-600 h-2 rounded-full transition-all duration-1000"
                                             style="width: {{ ($timerSeconds / 60) * 100 }}%">
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Time remaining: {{ floor($timerSeconds / 60) }}:{{ str_pad($timerSeconds % 60, 2, '0', STR_PAD_LEFT) }}
                                    </p>
                                @endif

                                <button wire:click="cancelCheckout" 
                                        class="mt-6 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                    Cancel and return to resource
                                </button>
                            </div>

                        {{-- Payment Step: Success --}}
                        @elseif($paymentStep === 'success')
                            <div class="text-center py-8 animate-fade-in">
                                <div class="mb-6">
                                    <div class="w-20 h-20 mx-auto bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                                        <svg class="w-10 h-10 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                                
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                    Payment Successful!
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 mb-6">
                                    Thank you for your purchase. Redirecting you to download your resource...
                                </p>

                                <div class="flex justify-center gap-3">
                                    <div class="w-2 h-2 bg-primary-600 rounded-full animate-bounce"></div>
                                    <div class="w-2 h-2 bg-primary-600 rounded-full animate-bounce delay-100"></div>
                                    <div class="w-2 h-2 bg-primary-600 rounded-full animate-bounce delay-200"></div>
                                </div>
                            </div>

                        {{-- Payment Step: Failed --}}
                        @elseif($paymentStep === 'failed')
                            <div class="text-center py-8 animate-fade-in">
                                <div class="mb-6">
                                    <div class="w-20 h-20 mx-auto bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                                        <svg class="w-10 h-10 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                </div>
                                
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                    Payment Failed
                                </h3>
                                <p class="text-red-600 dark:text-red-400 mb-4">
                                    {{ $errorMessage }}
                                </p>

                                <div class="flex gap-3 justify-center">
                                    <button wire:click="retryPayment" 
                                            class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition">
                                        Try Again
                                    </button>
                                    <button wire:click="cancelCheckout" 
                                            class="px-6 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-medium rounded-lg transition">
                                        Cancel
                                    </button>
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

                    {{-- Manual Entry (for testing/admin) --}}
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
                            <textarea type="text" wire:model="transactionId" placeholder="MPESA Message/Ref No."
                                    class="w-full px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg"></textarea>
                            <button wire:click="manualConfirmPayment" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg">
                                Confirm Payment Manually
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 sticky top-24">
                    
                    {{-- Help Section --}}
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        Need Help?
                    </h3>
                    
                    <div class="space-y-4 text-sm">
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-primary-100 dark:bg-primary-900/30 rounded-lg">
                                <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white mb-1">
                                    M-PESA Support
                                </h4>
                                <p class="text-gray-500 dark:text-gray-400">
                                    Having issues with payment? Contact our support team.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-primary-100 dark:bg-primary-900/30 rounded-lg">
                                <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white mb-1">
                                    Payment Timeout
                                </h4>
                                <p class="text-gray-500 dark:text-gray-400">
                                    If you don't enter PIN within 60 seconds, the transaction will timeout.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-primary-100 dark:bg-primary-900/30 rounded-lg">
                                <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M9 12l2 2 4-5m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white mb-1">
                                    Instant Delivery
                                </h4>
                                <p class="text-gray-500 dark:text-gray-400">
                                    Once payment is confirmed, you'll be redirected to download immediately.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span>Secure SSL Encrypted</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('startTimer', () => {
                console.log('Timer started');
            });

            Livewire.on('redirectToSuccess', (data) => {
                setTimeout(() => {
                    window.location.href = '/checkout/success/' + data.order;
                }, 3000);
            });
        });
    </script>
</div>