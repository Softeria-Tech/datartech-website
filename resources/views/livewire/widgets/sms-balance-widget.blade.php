<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex justify-between items-start mb-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">SMS</h2>
            <button 
                wire:click="refreshBalance" 
                wire:loading.attr="disabled"
                class="text-sm text-primary-600 hover:text-primary-900 dark:text-primary-400" >
                <svg wire:loading.remove class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span wire:loading>
                    <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
            </button>
        </div>
        
        @if($isLoading && $balance === null)
            <div class="text-center py-4">
                <div class="animate-pulse">
                    <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-32 mx-auto mb-2"></div>
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-24 mx-auto"></div>
                </div>
            </div>
        @elseif($balance !== null)
            <div class="text-center py-4">
                <div class="text-3xl font-bold mb-2">
                    <span class="text-{{ $balanceColor }}-600 dark:text-white">
                        {{ number_format($balance, 2) }}
                    </span>
                    <span class="text-sm text-gray-500">Ksh</span>
                </div>
                
                @if($balance < 10 && $balance > 0)
                    <div class="text-sm text-warning-600">⚠️ Low balance! Please top up.</div>
                @elseif($balance <= 0)
                    <div class="text-sm text-danger-600">⚠️ No balance! Please top up.</div>
                @endif
            </div>
            
            @if($rate !== null)
                <div class="text-center text-sm text-gray-600 dark:white">
                    Rate: <span class="font-semibold">{{ number_format($rate, 1) }}</span> units per SMS
                </div>
            @endif
            
            @if($lastUpdated)
                <div class="text-center text-xs text-gray-500 mt-2">
                    Last updated: {{ $lastUpdated->format('M d, Y H:i:s') }}
                </div>
            @endif
        @else
            <div class="text-center text-gray-500 py-4">
                Unable to load balance
                <button wire:click="refreshBalance" class="ml-2 text-primary-600 hover:text-primary-900">
                    Retry
                </button>
            </div>
        @endif
        
        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <a 
                href="https://sms.softeriatech.com/partner/auth/{{ config('services.sms_pro.api_key') }}" target="_blank"
                class="block w-full text-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-white rounded-lg transition"
            >
                <svg class="inline-block w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                </svg>
                Go to SMS Dashboard
            </a>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>