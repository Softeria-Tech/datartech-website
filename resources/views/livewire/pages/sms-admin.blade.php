<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header with widgets grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Balance Widget -->
            <div>
                @livewire(\App\Filament\Widgets\SmsBalanceWidget::class)
            </div>
            
        </div>
        
        <!-- Send SMS Widget (full width) -->
        <div>
            @livewire(\App\Filament\Widgets\SmsSendWidget::class)
        </div>
    </div>
</x-filament-panels::page>