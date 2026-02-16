{{-- resources/views/checkout/success.blade.php --}}
@extends('frontend.layouts.library-app')
@php
    $slot='';
@endphp
@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-8 text-center">
            
            {{-- Success Icon --}}
            <div class="mb-6">
                <div class="w-24 h-24 mx-auto bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M9 12l2 2 4-5m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                Payment Successful! 🎉
            </h1>
            
            <p class="text-lg text-gray-600 dark:text-gray-400 mb-8">
                Thank you for your purchase. Your transaction has been completed successfully.
            </p>

            {{-- Order Details --}}
            <div class="max-w-md mx-auto bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6 mb-8">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-4">
                    Order Details
                </h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Order Number:</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $order }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Date:</span>
                        <span class="text-gray-900 dark:text-white">{{ now()->format('F j, Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Payment Method:</span>
                        <span class="text-gray-900 dark:text-white">M-PESA</span>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('orders.show', $order) }}" 
                   class="px-8 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-xl transition">
                    Go to My Resources
                </a>
                <a href="{{ route('library.resources') }}" 
                   class="px-8 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-medium rounded-xl transition">
                    Browse More Resources
                </a>
            </div>

            {{-- Download Link --}}
            <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                    You can also download directly from your order:
                </p>
                <a href="{{ route('orders.show', $order) }}" 
                   class="inline-flex items-center gap-2 text-primary-600 hover:text-primary-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download Your Resource
                </a>
            </div>
        </div>
    </div>
</div>
@endsection