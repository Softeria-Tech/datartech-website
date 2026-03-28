<?php

namespace App\Filament\Resources\ResourceResource\Pages;

use App\Filament\Resources\ResourceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Filament\Notifications\Notification;

class ListResources extends ListRecords
{
    protected static string $resource = ResourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Resource')
                ->icon('heroicon-o-plus'),
            Actions\Action::make('import')
                ->label('Import Resources')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('gray'),
        ];
    }
    
    protected function getTableBulkActions(): array
    {
        return [
            BulkAction::make('delete')
                ->label('Delete Selected')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function (Collection $records) {
                    $fileCount = 0;
                    $recordCount = $records->count();
                    
                    foreach ($records as $record) {
                        // Count files for notification
                        if ($record->file_path) $fileCount++;
                        if ($record->preview_file_path) $fileCount++;
                        if ($record->thumbnail) $fileCount++;
                        if ($record->cover_image) $fileCount++;
                        
                        // Delete the resource (files will be auto-deleted by model)
                        $record->delete();
                    }
                    
                    Notification::make()
                        ->title('Resources Deleted')
                        ->body("Deleted {$recordCount} resource(s) and {$fileCount} associated file(s).")
                        ->success()
                        ->send();
                }),
            BulkAction::make('toggle_publish')
                ->label('Toggle Publish Status')
                ->icon('heroicon-o-eye')
                ->action(function ($records) {
                    foreach ($records as $record) {
                        $record->update([
                            'is_published' => !$record->is_published
                        ]);
                    }
                    
                    Notification::make()
                        ->title('Status Updated')
                        ->body('Publication status updated for selected resources.')
                        ->success()
                        ->send();
                }),
            BulkAction::make('delete_files_only')
                ->label('Delete Files Only')
                ->icon('heroicon-o-cloud-arrow-down')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function (Collection $records) {
                    $deletedFiles = 0;
                    
                    foreach ($records as $record) {
                        $record->deleteFiles();
                        $record->update([
                            'file_path' => null,
                            'preview_file_path' => null,
                            'file_name' => null,
                            'file_size' => null,
                        ]);
                        $deletedFiles++;
                    }
                    
                    Notification::make()
                        ->title('Files Deleted')
                        ->body("Deleted files for {$deletedFiles} resource(s).")
                        ->success()
                        ->send();
                }),
        ];
    }
}