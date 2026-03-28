<?php

namespace App\Filament\Resources\ResourceResource\Pages;

use App\Filament\Resources\ResourceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditResource extends EditRecord
{
    protected static string $resource = ResourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->before(function ($record) {
                    // Files will be automatically deleted by the model's boot method
                    // But we can add a confirmation message
                    $fileCount = 0;
                    if ($record->file_path) $fileCount++;
                    if ($record->preview_file_path) $fileCount++;
                    if ($record->thumbnail) $fileCount++;
                    if ($record->cover_image) $fileCount++;
                    
                    if ($fileCount > 0) {
                        Notification::make()
                            ->title('Deleting Files')
                            ->body("This resource has {$fileCount} associated file(s) that will be permanently deleted.")
                            ->warning()
                            ->send();
                    }
                })
                ->after(function ($record) {
                    Notification::make()
                        ->title('Resource Deleted')
                        ->body('Resource and all associated files have been deleted successfully.')
                        ->success()
                        ->send();
                }),
            Actions\Action::make('preview')
                ->label('Preview')
                ->icon('heroicon-o-eye')
                ->url(fn ($record) => route('resources.show', $record->slug))
                ->openUrlInNewTab(),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}