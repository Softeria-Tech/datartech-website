<?php

namespace App\Filament\Resources\BulkUploadResource\Pages;

use App\Filament\Resources\BulkUploadResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBulkUpload extends ViewRecord
{
    protected static string $resource = BulkUploadResource::class;
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('edit')
                ->label('Edit')
                ->icon('heroicon-o-pencil')
                ->url(fn () => null) // Disabled since we're using infolist
                ->disabled(),
            Actions\DeleteAction::make()
                ->before(function ($record) {
                    $record->deleteAllResources();
                }),
        ];
    }
    
    protected function getHeaderWidgets(): array
    {
        return [
            // You can add widgets here if needed
        ];
    }
    
    protected function getFooterWidgets(): array
    {
        return [];
    }
}