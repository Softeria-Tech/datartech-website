<?php

namespace App\Filament\Resources\SubscriptionResource\Pages;

use App\Filament\Resources\SubscriptionResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateSubscription extends CreateRecord
{
    protected static string $resource = SubscriptionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set starts_at if not set
        if (empty($data['starts_at'])) {
            $data['starts_at'] = now();
        }
        
        // Set ends_at for lifetime plan
        if (($data['plan'] ?? null) === 'lifetime') {
            $data['ends_at'] = null;
        }
        
        // Generate order_id if not set
        if (empty($data['order_id'])) {
            $data['order_id'] = 'SUB-' . strtoupper(Str::random(10));
        }
        
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Subscription created successfully';
    }
}