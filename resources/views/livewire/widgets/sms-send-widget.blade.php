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
            <!-- User Selection Mode -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">
                    Send To *
                </label>
                <div class="space-y-2">
                    <label class="inline-flex items-center">
                        <input type="radio" wire:model.live="userSelectionMode" value="manual" class="form-radio text-primary-600">
                        <span class="ml-2 text-sm text-gray-700 dark:text-white">Manual Entry</span>
                    </label>
                    <label class="inline-flex items-center ml-4">
                        <input type="radio" wire:model.live="userSelectionMode" value="all" class="form-radio text-primary-600">
                        <span class="ml-2 text-sm text-gray-700 dark:text-white">All Active Users</span>
                    </label>
                    <label class="inline-flex items-center ml-4">
                        <input type="radio" wire:model.live="userSelectionMode" value="filter" class="form-radio text-primary-600">
                        <span class="ml-2 text-sm text-gray-700 dark:text-white">Filtered Users</span>
                    </label>
                </div>
            </div>
            
            <!-- Manual Entry Mode -->
            @if($userSelectionMode === 'manual')
            <div>
                <label for="mobiles" class="block text-sm font-medium text-gray-700 dark:text-white mb-2">
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
            @endif
            
            <!-- Filter Mode -->
            @if($userSelectionMode === 'filter')
            <div class="space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">
                            Filter Type
                        </label>
                        <select wire:model.live="filterType" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="all">All Active Users</option>
                            <option value="expired_subscriptions">Users with Expired Subscriptions</option>
                            <option value="pending_orders">Users with Pending Orders</option>
                            <option value="new_users">New Users (Last 7 Days)</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">
                            Items Per Page
                        </label>
                        <select wire:model.live="perPage" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="10">10 per page</option>
                            <option value="25">25 per page</option>
                            <option value="50">50 per page</option>
                            <option value="100">100 per page</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-white mb-2">
                        Search Users
                    </label>
                    <input type="text" wire:model.live.debounce.300ms="userSearch"
                        placeholder="Search by name, email, or phone..."
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    />
                </div>
                
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Filter Applied</span>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $this->filterStats['description'] }}</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Total Available</span>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($this->filterStats['count']) }} users</p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Currently Selected</span>
                            <p class="text-sm font-semibold text-primary-600 dark:text-primary-400">{{ number_format($totalSelectedCount) }} users</p>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap gap-2 mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" wire:click="selectAllFiltered" wire:loading.attr="disabled" class="px-3 py-1 text-xs bg-primary-100 text-primary-700 hover:bg-primary-200 rounded-md transition">
                            Select All ({{ number_format($this->filterStats['count']) }})
                        </button>
                        <button type="button" wire:click="selectCurrentPage" wire:loading.attr="disabled" class="px-3 py-1 text-xs bg-blue-100 text-blue-700 hover:bg-blue-200 rounded-md transition">
                            Select Current Page ({{ $perPage }})
                        </button>
                        <button type="button" wire:click="clearSelection" class="px-3 py-1 text-xs bg-danger-100 text-danger-700 hover:bg-danger-200 rounded-md transition">
                            Clear Selection
                        </button>
                    </div>
                </div>
                
                @if($this->getAvailableUsersProperty()->count() > 0)
                <div class="border rounded-lg overflow-hidden">
                    <div class="bg-gray-50 dark:bg-gray-800 px-4 py-2 border-b flex justify-between items-center">
                        <span class="text-sm font-medium">Select Users</span>
                        <span class="text-xs text-gray-500">Page {{ $this->getAvailableUsersProperty()->currentPage() }} of {{ $this->getAvailableUsersProperty()->lastPage() }}</span>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        @foreach($this->getAvailableUsersProperty() as $user)
                        <label class="flex items-center px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer border-b">
                            <input  type="checkbox" wire:click="toggleUserSelection({{ $user->id }})"
                                {{ in_array($user->id, $selectedUsers) ? 'checked' : '' }}
                                class="form-checkbox text-primary-600 rounded"
                            >
                            <div class="px-4 flex-1">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $user->name }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $user->email }} | {{ $user->phone ?? 'No phone' }}
                                </div>
                            </div>
                            @if(in_array($user->id, $selectedUsers))
                                <span class="text-xs text-primary-600">Selected</span>
                            @endif
                        </label>
                        @endforeach
                    </div>
                    
                    <!-- Pagination Links -->
                    <div class="bg-gray-50 dark:bg-gray-800 px-4 py-2 border-t">
                        {{ $this->getAvailableUsersProperty()->links() }}
                    </div>
                </div>
                @else
                <div class="text-center py-8 text-gray-500">
                    <p>No users found matching your criteria.</p>
                    <p class="text-xs mt-1">Try adjusting your search or filter settings.</p>
                </div>
                @endif
            </div>
            @endif
            
            <!-- All Active Users Mode -->
            @if($userSelectionMode === 'all')
            <div class="bg-primary-50 dark:bg-primary-900/20 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-primary-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <div>
                        <div class="text-sm font-medium text-primary-900 dark:text-primary-100">
                            Sending to all active users
                        </div>
                        <div class="text-xs text-primary-700 dark:text-primary-300">
                            Total active users: {{ number_format(\App\Models\User::where('is_active', true)->whereNotNull('phone')->where('phone', '!=', '')->count()) }} users with phone numbers
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Sender Name -->
            <div>
                <label for="senderName" class="block text-sm font-medium text-gray-700 dark:text-white mb-2">
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
            
            <!-- Message -->
            <div>
                <div class="flex justify-between items-center mb-2">
                    <label for="message" class="block text-sm font-medium text-gray-700 dark:text-white">
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
            
            <!-- Estimated Cost -->
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
            
            <!-- Submit Button -->
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