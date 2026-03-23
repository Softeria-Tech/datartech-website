<?php

namespace App\Filament\Resources\ResourceGroupResource\Pages;

use App\Filament\Resources\ResourceGroupResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateResourceGroup extends CreateRecord
{
    protected static string $resource = ResourceGroupResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        
        return $data;
    }
}