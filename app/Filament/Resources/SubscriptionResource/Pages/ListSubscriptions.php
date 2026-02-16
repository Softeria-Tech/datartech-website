<?php

namespace App\Filament\Resources\SubscriptionResource\Pages;

use App\Filament\Resources\SubscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSubscriptions extends ListRecords
{
    protected static string $resource = SubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Subscription')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Subscriptions'),
            'active' => Tab::make('Active')
                ->modifyQueryUsing(fn (Builder $query) => $query->active()),
            'trial' => Tab::make('Trial')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('trial_ends_at')
                    ->where('trial_ends_at', '>', now())),
            'cancelled' => Tab::make('Cancelled')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('cancelled_at')),
            'expired' => Tab::make('Expired')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('ends_at')
                    ->where('ends_at', '<', now())
                    ->whereNull('cancelled_at')),
            'lifetime' => Tab::make('Lifetime')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('plan', 'lifetime')),
        ];
    }
}