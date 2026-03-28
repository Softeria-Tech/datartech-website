<?php

namespace App\Filament\Resources\ResourceResource\Pages;

use App\Filament\Resources\ResourceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;

class ViewResource extends ViewRecord
{
    protected static string $resource = ResourceResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Resource Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('title'),
                                TextEntry::make('slug'),
                                TextEntry::make('category.name'),
                                TextEntry::make('price')
                                    ->formatStateUsing(function ($state) {
                                        if (!$state || $state == 0) {
                                            return 'Free';
                                        }
                                        return 'Ksh ' . number_format($state, 0);
                                    })
                                    ->color(fn ($state) => (!$state || $state == 0) ? 'success' : 'primary')
                                    ->weight(fn ($state) => (!$state || $state == 0) ? 'bold' : 'normal'),
                                TextEntry::make('discount_price')->money('Ksh'),
                                TextEntry::make('requires_subscription')
                                    ->badge()
                                    ->formatStateUsing(fn (bool $state): string => $state ? 'Subscription Required' : 'One-time Purchase')
                                    ->color(fn (bool $state): string => $state ? 'warning' : 'success')
                                    ->icon(fn (bool $state): string => $state ? 'heroicon-m-lock-closed' : 'heroicon-m-lock-open'),
                                TextEntry::make('is_published')
                                    ->badge()
                                    ->formatStateUsing(fn (bool $state): string => $state ? 'Published' : 'Draft')
                                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                                    ->icon(fn (bool $state): string => $state ? 'heroicon-m-check-circle' : 'heroicon-m-x-circle'),
                                TextEntry::make('download_count'),
                            ]),
                    ]),
                    
                Section::make('Description')
                    ->schema([
                        TextEntry::make('description')
                            ->html()
                            ->columnSpanFull(),
                    ]),
                    
                Section::make('Files')
                    ->description('Resource files and downloads')
                    ->icon('heroicon-o-document')
                    ->schema([
                        // MAIN FILE
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('file_path')
                                    ->label('Main File')
                                    ->formatStateUsing(fn ($state) => $state ? basename($state) : 'No file uploaded')
                                    ->badge()
                                    ->color(fn ($state) => $state ? 'success' : 'danger')
                                    ->columnSpan(1),
                                
                                // File preview & download actions
                                \Filament\Infolists\Components\Actions::make([
                                    \Filament\Infolists\Components\Actions\Action::make('preview_file')
                                        ->label('Preview')
                                        ->icon('heroicon-o-eye')
                                        ->color('info')
                                        ->url(fn ($record) => $record->file_path ? Storage::url($record->file_path) : '#')
                                        ->openUrlInNewTab()
                                        ->visible(fn ($record) => $record->file_path && in_array(pathinfo($record->file_path, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'pdf']))
                                        ->button(),
                                    
                                    \Filament\Infolists\Components\Actions\Action::make('download_file')
                                        ->label('Download')
                                        ->icon('heroicon-o-arrow-down-tray')
                                        ->color('primary')
                                        ->url(fn ($record) => $record->file_path ? Storage::url($record->file_path) : '#')
                                        ->openUrlInNewTab()
                                        ->visible(fn ($record) => (bool) $record->file_path)
                                        ->button(),
                                        
                                    \Filament\Infolists\Components\Actions\Action::make('delete_file')
                                        ->label('Delete File')
                                        ->icon('heroicon-o-trash')
                                        ->color('danger')
                                        ->requiresConfirmation()
                                        ->visible(fn ($record) => (bool) $record->file_path)
                                        ->action(function ($record) {
                                            if ($record->file_path && Storage::disk('public')->exists($record->file_path)) {
                                                Storage::disk('public')->delete($record->file_path);
                                                $record->update([
                                                    'file_path' => null,
                                                    'file_name' => null,
                                                    'file_size' => null,
                                                ]);
                                                
                                                Notification::make()
                                                    ->title('File Deleted')
                                                    ->body('The main file has been deleted successfully.')
                                                    ->success()
                                                    ->send();
                                            }
                                        })
                                        ->button(),
                                ])->columnSpan(1),
                            ]),
                        
                        // FILE METADATA
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('file_size')
                                    ->label('File Size')
                                    ->formatStateUsing(fn ($state) => $state ? ResourceResource::formatBytes($state) : 'Unknown')
                                    ->badge()
                                    ->color('gray'),
                                
                                TextEntry::make('file_name')
                                    ->label('Original Name')
                                    ->formatStateUsing(fn ($state) => $state ?? 'N/A')
                                    ->copyable()
                                    ->copyMessage('Filename copied'),
                                
                                TextEntry::make('download_count')
                                    ->label('Downloads')
                                    ->numeric()
                                    ->badge()
                                    ->color('warning')
                                    ->suffix(' times'),
                            ]),
                        
                        // PREVIEW FILE SECTION
                        \Filament\Infolists\Components\Fieldset::make('Preview File')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('preview_file_path')
                                            ->label('Preview File')
                                            ->formatStateUsing(fn ($state) => $state ? basename($state) : 'No preview file')
                                            ->badge()
                                            ->color(fn ($state) => $state ? 'warning' : 'gray')
                                            ->columnSpan(1),
                                        
                                        \Filament\Infolists\Components\Actions::make([
                                            \Filament\Infolists\Components\Actions\Action::make('preview_preview')
                                                ->label('Preview')
                                                ->icon('heroicon-o-eye')
                                                ->color('info')
                                                ->url(fn ($record) => $record->preview_file_path ? Storage::url($record->preview_file_path) : '#')
                                                ->openUrlInNewTab()
                                                ->visible(fn ($record) => (bool) $record->preview_file_path)
                                                ->button(),
                                            
                                            \Filament\Infolists\Components\Actions\Action::make('download_preview')
                                                ->label('Download')
                                                ->icon('heroicon-o-arrow-down-tray')
                                                ->color('warning')
                                                ->url(fn ($record) => $record->preview_file_path ? Storage::url($record->preview_file_path) : '#')
                                                ->openUrlInNewTab()
                                                ->visible(fn ($record) => (bool) $record->preview_file_path)
                                                ->button(),
                                                
                                            \Filament\Infolists\Components\Actions\Action::make('delete_preview')
                                                ->label('Delete Preview')
                                                ->icon('heroicon-o-trash')
                                                ->color('danger')
                                                ->requiresConfirmation()
                                                ->visible(fn ($record) => (bool) $record->preview_file_path)
                                                ->action(function ($record) {
                                                    if ($record->preview_file_path && Storage::disk('public')->exists($record->preview_file_path)) {
                                                        Storage::disk('public')->delete($record->preview_file_path);
                                                        $record->update(['preview_file_path' => null]);
                                                        
                                                        Notification::make()
                                                            ->title('Preview File Deleted')
                                                            ->body('The preview file has been deleted successfully.')
                                                            ->success()
                                                            ->send();
                                                    }
                                                })
                                                ->button(),
                                        ])->columnSpan(1),
                                    ]),
                            ])
                            ->visible(fn ($record) => $record->preview_file_path || $record->delivery_type === 'both'),
                        
                        // COVER IMAGE SECTION
                        \Filament\Infolists\Components\Fieldset::make('Images')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('thumbnail')
                                            ->label('Thumbnail')
                                            ->formatStateUsing(fn ($state) => $state ? basename($state) : 'No thumbnail')
                                            ->badge()
                                            ->color(fn ($state) => $state ? 'success' : 'gray'),
                                            
                                        \Filament\Infolists\Components\Actions::make([
                                            \Filament\Infolists\Components\Actions\Action::make('delete_thumbnail')
                                                ->label('Delete Thumbnail')
                                                ->icon('heroicon-o-trash')
                                                ->color('danger')
                                                ->requiresConfirmation()
                                                ->visible(fn ($record) => (bool) $record->thumbnail)
                                                ->action(function ($record) {
                                                    if ($record->thumbnail && Storage::disk('public')->exists($record->thumbnail)) {
                                                        Storage::disk('public')->delete($record->thumbnail);
                                                        $record->update(['thumbnail' => null]);
                                                        
                                                        Notification::make()
                                                            ->title('Thumbnail Deleted')
                                                            ->body('The thumbnail has been deleted successfully.')
                                                            ->success()
                                                            ->send();
                                                    }
                                                })
                                                ->button(),
                                        ]),
                                        
                                        TextEntry::make('cover_image')
                                            ->label('Cover Image')
                                            ->formatStateUsing(fn ($state) => $state ? basename($state) : 'No cover image')
                                            ->badge()
                                            ->color(fn ($state) => $state ? 'success' : 'gray'),
                                            
                                        \Filament\Infolists\Components\Actions::make([
                                            \Filament\Infolists\Components\Actions\Action::make('delete_cover')
                                                ->label('Delete Cover')
                                                ->icon('heroicon-o-trash')
                                                ->color('danger')
                                                ->requiresConfirmation()
                                                ->visible(fn ($record) => (bool) $record->cover_image)
                                                ->action(function ($record) {
                                                    if ($record->cover_image && Storage::disk('public')->exists($record->cover_image)) {
                                                        Storage::disk('public')->delete($record->cover_image);
                                                        $record->update(['cover_image' => null]);
                                                        
                                                        Notification::make()
                                                            ->title('Cover Image Deleted')
                                                            ->body('The cover image has been deleted successfully.')
                                                            ->success()
                                                            ->send();
                                                    }
                                                })
                                                ->button(),
                                        ]),
                                    ]),
                            ])
                            ->visible(fn ($record) => $record->thumbnail || $record->cover_image),
                        
                        // EXTERNAL URL SECTION
                        \Filament\Infolists\Components\Fieldset::make('External URL')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('external_url')
                                            ->label('URL')
                                            ->formatStateUsing(fn ($state) => $state ?: 'No external URL')
                                            ->copyable()
                                            ->copyMessage('URL copied')
                                            ->color(fn ($state) => $state ? 'primary' : 'gray')
                                            ->columnSpan(1),
                                        
                                        \Filament\Infolists\Components\Actions::make([
                                            \Filament\Infolists\Components\Actions\Action::make('visit_url')
                                                ->label('Visit URL')
                                                ->icon('heroicon-o-link')
                                                ->color('success')
                                                ->url(fn ($record) => $record->external_url)
                                                ->openUrlInNewTab()
                                                ->visible(fn ($record) => (bool) $record->external_url)
                                                ->button(),
                                        ])->columnSpan(1),
                                    ]),
                            ])
                            ->visible(fn ($record) => in_array($record->delivery_type, ['url', 'both'])),
                    ])
                    ->collapsible(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make()
                ->before(function ($record) {
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
                }),
        ];
    }
}