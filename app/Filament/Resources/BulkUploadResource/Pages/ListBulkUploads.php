<?php

namespace App\Filament\Resources\BulkUploadResource\Pages;

use App\Filament\Resources\BulkUploadResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBulkUploads extends ListRecords
{
    protected static string $resource = BulkUploadResource::class;

    protected function getHeaderActions(): array
    {
        return [                
            Actions\Action::make('view_upload_page')
                ->label('Upload Files')
                ->icon('heroicon-o-cloud-arrow-up')
                ->color('success')
                ->url(fn (): string => route('filament.admin.resources.resource-groups.bulk-upload', ['record' => 1]))
        ];
    }
}