<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class SmsAdmin extends Page
{
    protected static string $view = 'livewire.pages.sms-admin';
    
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    
    protected static ?string $navigationLabel = 'Bulk SMS';
    
    protected static ?string $title = 'SMS Management';
    
    protected static ?int $navigationSort = 1;
    
    //protected static ?string $navigationGroup = 'Admin';
    
    protected static ?string $slug = 'sms-admin';
    
    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\SmsBalanceWidget::class,
            \App\Filament\Widgets\SmsSendWidget::class,
        ];
    }
    
    public function getColumns(): int | string | array
    {
        return 2;
    }
}