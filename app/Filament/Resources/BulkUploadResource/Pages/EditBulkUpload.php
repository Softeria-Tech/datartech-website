<?php

namespace App\Filament\Resources\BulkUploadResource\Pages;

use App\Filament\Resources\BulkUploadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditBulkUpload extends EditRecord
{
    protected static string $resource = BulkUploadResource::class;
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('view_resources')
                ->label('View Resources')
                ->icon('heroicon-o-document-text')
                ->color('info')
                ->url(fn (): string => 
                    $this->record->resources()->exists() 
                        ? route('filament.admin.resources.resources.index', ['tableFilters[bulk_upload_id][value]' => $this->record->id])
                        : '#'
                )
                ->disabled(fn () => !$this->record->resources()->exists()),
                
            Actions\DeleteAction::make()
                ->before(function () {
                    $this->record->deleteAllResources();
                }),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function getSavedNotificationTitle(): ?string
    {
        return 'Bulk upload record updated successfully';
    }
    
    protected function afterSave(): void
    {
        Notification::make()
            ->title('Bulk Upload Updated')
            ->body('The bulk upload record has been updated successfully.')
            ->success()
            ->send();
    }
}