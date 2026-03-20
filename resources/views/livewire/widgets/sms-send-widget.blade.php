<x-filament-widgets::widget>
    <x-filament::section>
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Send SMS</h2>
        
        @if(session()->has('sms_sent'))
            <div class="mb-4 p-4 bg-success-50 border border-success-200 text-success-700 rounded-lg">
                {{ session('sms_sent') }}
            </div>
        @endif
        
        @if($errorMessage)
            <div class="mb-4 p-4 bg-danger-50 border border-danger-200 text-danger-700 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <span class="font-semibold">Error:</span> {{ $errorMessage }}
                        @if(str_contains($errorMessage, 'balance'))
                            <div class="mt-2">
                                <a href="{{ config('services.sms_pro.dashboard_url', '#') }}" target="_blank" class="text-sm text-danger-800 underline">
                                    Click here to top up your balance
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
        
        <form wire:submit="sendSms" class="space-y-4">
            <div>
                <label for="mobiles" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Phone Numbers *
                </label>
                <textarea 
                    wire:model="mobiles" 
                    id="mobiles" 
                    rows="3"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    placeholder="Enter phone numbers (separate with commas, new lines, or semicolons)&#10;Example: 1234567890, 0987654321&#10;254712345678"
                ></textarea>
                @error('mobiles') <p class="text-sm text-danger-600 mt-1">{{ $message }}</p> @enderror
                <p class="text-xs text-gray-500 mt-1">
                    Supported format: 10-14 digits. Multiple numbers: separate with comma, new line, or semicolon
                </p>
            </div>
            
            <div>
                <label for="senderName" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Sender Name *
                </label>
                <input 
                    type="text" 
                    wire:model="senderName" 
                    id="senderName"
                    maxlength="11"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    placeholder="Max 11 characters"
                />
                @error('senderName') <p class="text-sm text-danger-600 mt-1">{{ $message }}</p> @enderror
                <p class="text-xs text-gray-500 mt-1">Maximum 11 characters (alphanumeric)</p>
            </div>
            
            <div>
                <div class="flex justify-between items-center mb-2">
                    <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Message *
                    </label>
                    <span class="text-xs text-gray-500">
                        {{ $this->messageLength }} / 160 chars | {{ $this->messageSegments }} segment(s)
                    </span>
                </div>
                <textarea 
                    wire:model="message" 
                    id="message" 
                    rows="4"
                    maxlength="918"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    placeholder="Enter your message here..."
                ></textarea>
                @error('message') <p class="text-sm text-danger-600 mt-1">{{ $message }}</p> @enderror
            </div>
            
            @if($this->estimatedCost > 0)
                <div class="p-3 bg-primary-50 dark:bg-primary-900/20 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-primary-700 dark:text-primary-300">Estimated Cost:</span>
                        <span class="text-lg font-bold text-primary-700 dark:text-primary-300">
                            {{ number_format($this->estimatedCost, 2) }} units
                        </span>
                    </div>
                </div>
            @endif
            
            <div class="flex justify-end">
                <button 
                    type="submit" 
                    wire:loading.attr="disabled"
                    wire:target="sendSms"
                    class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span wire:loading.remove wire:target="sendSms">Send SMS</span>
                    <span wire:loading wire:target="sendSms">
                        <svg class="animate-spin inline-block w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Sending...
                    </span>
                </button>
            </div>
        </form>
    </x-filament::section>
</x-filament-widgets::widget>