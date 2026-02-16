<?php

namespace App\Filament\Widgets;

use App\Models\Resource;
use App\Models\Order;
use App\Models\Subscription;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ResourceStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Resources', Resource::count())
                ->description('Active resources')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success'),
                
            Stat::make('Published', Resource::where('is_published', true)->count())
                ->description(Resource::where('is_published', false)->count() . ' drafts')
                ->descriptionIcon('heroicon-m-eye')
                ->color('info'),
                
            Stat::make('Total Downloads', Resource::sum('download_count'))
                ->description('All time')
                ->descriptionIcon('heroicon-m-arrow-down-tray')
                ->color('warning'),
                
            Stat::make('Orders', 'Ksh ' . number_format(Order::where('payment_status', 'paid')->sum('total'), 2))
                ->description('From ' . Order::where('payment_status', 'paid')->count() . ' sales')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Subscriptions', 'Ksh ' . number_format(Subscription::sum('price'), 2))
                ->description('From ' . Subscription::count())
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}