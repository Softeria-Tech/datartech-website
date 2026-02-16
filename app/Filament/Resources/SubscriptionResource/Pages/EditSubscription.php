<?php

namespace App\Filament\Resources\SubscriptionResource\Pages;

use App\Filament\Resources\SubscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubscription extends EditRecord
{
    protected static string $resource = SubscriptionResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Handle lifetime plan
        if (($data['plan'] ?? null) === 'lifetime') {
            $data['ends_at'] = null;
        }
        
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\Action::make('cancel')
                ->label('Cancel Subscription')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update([
                        'cancelled_at' => now(),
                        'ends_at' => $this->record->ends_at ?? now(),
                    ]);
                    $this->notify('success', 'Subscription cancelled successfully');
                })
                ->visible(fn () => $this->record->isActive() && !$this->record->isCancelled()),
            
            Actions\Action::make('reactivate')
                ->label('Reactivate Subscription')
                ->icon('heroicon-o-arrow-path')
                ->color('success')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update([
                        'cancelled_at' => null,
                        'ends_at' => $this->record->plan === 'lifetime' 
                            ? null 
                            : ($this->record->ends_at ?: now()->addYear()),
                    ]);
                    $this->notify('success', 'Subscription reactivated successfully');
                })
                ->visible(fn () => $this->record->isCancelled()),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}