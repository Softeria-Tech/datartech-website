<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($data['payment_status'] === 'paid' && empty($data['paid_at'])) {
            $data['paid_at'] = now();
        }
        
        if (isset($data['order_data']) && is_array($data['order_data'])) {
            $data['order_data'] = json_encode($data['order_data']);
        }
        
        return $data;
    }

    protected function afterSave(): void
    {
        // Create download access if order becomes paid
        if ($this->record->payment_status === 'paid') {
            $exists = $this->record->user->downloads()
                ->where('order_id', $this->record->id)
                ->exists();
                
            if (!$exists) {
                OrderResource::makeDownloadable($this->record);
            }
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\Action::make('mark_as_paid')
                ->label('Mark as Paid')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->action(function () {
                    $this->record->update([
                        'payment_status' => 'paid',
                        'paid_at' => now(),
                    ]);
                    $this->notify('success', 'Order marked as paid');
                })
                ->visible(fn () => $this->record->payment_status !== 'paid'),
            
            Actions\Action::make('mark_as_completed')
                ->label('Mark as Completed')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->action(function () {
                    $this->record->update([
                        'order_status' => 'completed',
                    ]);
                    $this->notify('success', 'Order marked as completed');
                })
                ->visible(fn () => $this->record->order_status !== 'completed'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}