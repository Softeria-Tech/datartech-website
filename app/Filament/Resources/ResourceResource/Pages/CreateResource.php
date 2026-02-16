<?php

namespace App\Filament\Resources\ResourceResource\Pages;

use App\Filament\Resources\ResourceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateResource extends CreateRecord
{
    protected static string $resource = ResourceResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure slug is set
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }
        
        // Set default meta title if not provided
        if (empty($data['meta_title'])) {
            $data['meta_title'] = $data['title'] . ' - Download Now';
        }
        
        return $data;
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}