<?php

namespace App\Filament\Resources\UserDownloadResource\Pages;

use App\Filament\Resources\UserDownloadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserDownload extends EditRecord
{
    protected static string $resource = UserDownloadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
