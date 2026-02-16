<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['order_number'])) {
            $data['order_number'] = 'ORD-' . strtoupper(Str::random(10));
        }
        
        if (empty($data['total_items'])) {
            $data['total_items'] = 1;
        }
        
        if (($data['payment_status'] ?? null) === 'paid' && empty($data['paid_at'])) {
            $data['paid_at'] = now();
        }

        if (isset($data['order_data']) && is_array($data['order_data'])) {
            $data['order_data'] = json_encode($data['order_data']);
        }
        
        return $data;
    }

    protected function afterCreate(): void
    {
        if ($this->record->payment_status === 'paid') {
            OrderResource::makeDownloadable($this->record);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}