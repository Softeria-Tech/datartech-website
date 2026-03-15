<?php

namespace App\Filament\Resources\ResourceGroupResource\Pages;

use App\Filament\Resources\ResourceGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditResourceGroup extends EditRecord
{
    protected static string $resource = ResourceGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\Action::make('bulk_upload')
                ->label('Bulk Upload')
                ->icon('heroicon-o-cloud-arrow-up')
                ->color('success')
                ->url(fn () => ResourceGroupResource::getUrl('bulk-upload', ['record' => $this->record])),
        ];
    }
}