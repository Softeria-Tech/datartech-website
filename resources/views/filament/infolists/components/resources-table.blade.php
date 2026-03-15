<div class="space-y-4">
    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div class="bg-primary-50 dark:bg-primary-900/20 rounded-lg p-4">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-primary-100 dark:bg-primary-800 rounded-full">
                    <x-heroicon-o-document-text class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Resources</p>
                    <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                        {{ $this->record->resources()->count() }}
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-warning-50 dark:bg-warning-900/20 rounded-lg p-4">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-warning-100 dark:bg-warning-800 rounded-full">
                    <x-heroicon-o-currency-dollar class="w-6 h-6 text-warning-600 dark:text-warning-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Paid Resources</p>
                    <p class="text-2xl font-bold text-warning-600 dark:text-warning-400">
                        {{ $this->record->resources()->where('price', '>', 0)->count() }}
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-success-50 dark:bg-success-900/20 rounded-lg p-4">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-success-100 dark:bg-success-800 rounded-full">
                    <x-heroicon-o-arrow-down-tray class="w-6 h-6 text-success-600 dark:text-success-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Downloads</p>
                    <p class="text-2xl font-bold text-success-600 dark:text-success-400">
                        {{ $this->record->resources()->sum('download_count') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{ $this->table }}
</div>