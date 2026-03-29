<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BulkUploadResource\Pages;
use App\Models\BulkUpload;
use App\Models\Category;
use App\Models\ResourceGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Notifications\Notification;

class BulkUploadResource extends Resource
{
    protected static ?string $model = BulkUpload::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-cloud-arrow-up';
    
    protected static ?string $navigationGroup = 'Content Management';
    
    protected static ?int $navigationSort = 20;

    /**
     * Use infolist for viewing instead of form
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Bulk Upload Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('category.path')
                                    ->label('Current Category')
                                    ->default('None')
                                    ->badge()
                                    ->color('primary'),
                                    
                                TextEntry::make('group.full_path')
                                    ->label('Current Group')
                                    ->default('None')
                                    ->badge()
                                    ->color('primary'),
                                    
                                TextEntry::make('originalCategory.path')
                                    ->label('Original Category')
                                    ->default('None')
                                    ->badge()
                                    ->color('gray'),
                                    
                                TextEntry::make('originalGroup.full_path')
                                    ->label('Original Group')
                                    ->default('None')
                                    ->badge()
                                    ->color('gray'),
                                    
                                TextEntry::make('price')
                                    ->label('Price')
                                    ->formatStateUsing(function ($state) {
                                        if (!$state || $state == 0) {
                                            return 'Free';
                                        }
                                        return 'Ksh ' . number_format($state, 0);
                                    })
                                    ->badge()
                                    ->color(fn ($state) => (!$state || $state == 0) ? 'success' : 'warning'),
                                    
                                TextEntry::make('total_files')
                                    ->label('Total Files')
                                    ->numeric()
                                    ->badge()
                                    ->color('info'),
                                    
                                TextEntry::make('successful_uploads')
                                    ->label('Successful Uploads')
                                    ->numeric()
                                    ->badge()
                                    ->color('success'),
                                    
                                TextEntry::make('failed_uploads')
                                    ->label('Failed Uploads')
                                    ->numeric()
                                    ->badge()
                                    ->color('danger'),
                                    
                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'pending' => 'warning',
                                        'processing' => 'info',
                                        'completed' => 'success',
                                        'failed' => 'danger',
                                        default => 'gray',
                                    }),
                                    
                                TextEntry::make('completed_at')
                                    ->label('Completed At')
                                    ->dateTime()
                                    ->placeholder('Not completed'),
                                    
                                TextEntry::make('uploader.name')
                                    ->label('Uploaded By')
                                    ->default('Unknown'),
                                    
                                TextEntry::make('created_at')
                                    ->label('Uploaded At')
                                    ->dateTime(),
                            ]),
                            
                        KeyValueEntry::make('metadata')
                            ->label('Metadata')
                            ->columnSpanFull()
                            ->visible(fn ($record) => !empty($record->metadata)),
                    ]),
                    
                Section::make('Statistics')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('total_resources')
                                    ->label('Total Resources Created')
                                    ->state(fn ($record): string => number_format($record->resources()->count()))
                                    ->badge()
                                    ->color('primary'),
                                    
                                TextEntry::make('total_file_size')
                                    ->label('Total File Size')
                                    ->state(fn ($record): string => self::formatBytes($record->resources()->sum('file_size')))
                                    ->badge()
                                    ->color('success'),
                                    
                                TextEntry::make('published_resources')
                                    ->label('Published Resources')
                                    ->state(fn ($record): string => number_format($record->resources()->where('is_published', true)->count()))
                                    ->badge()
                                    ->color('success'),
                                    
                                TextEntry::make('unpublished_resources')
                                    ->label('Unpublished Resources')
                                    ->state(fn ($record): string => number_format($record->resources()->where('is_published', false)->count()))
                                    ->badge()
                                    ->color('warning'),
                            ]),
                    ]),
                    
                Section::make('Batch Actions')
                    ->schema([
                        \Filament\Infolists\Components\Actions::make([
                            \Filament\Infolists\Components\Actions\Action::make('update_category_batch')
                                ->label('Update Category for All Resources')
                                ->icon('heroicon-o-tag')
                                ->color('warning')
                                ->form([
                                    Forms\Components\Select::make('new_category_id')
                                        ->label('New Category')
                                        ->options(function () {
                                            return Category::orderBy('name')
                                                ->get()
                                                ->mapWithKeys(fn ($cat) => [
                                                    $cat->id => $cat->path
                                                ]);
                                        })
                                        ->required()
                                        ->searchable(),
                                ])
                                ->action(function (array $data, $record) {
                                    $oldCategory = $record->category?->path ?? 'None';
                                    $record->updateResourcesCategory($data['new_category_id']);
                                    $newCategory = Category::find($data['new_category_id'])?->path;
                                    
                                    Notification::make()
                                        ->title('Category Updated')
                                        ->body("Category for all {$record->resources()->count()} resources changed from '{$oldCategory}' to '{$newCategory}'")
                                        ->success()
                                        ->send();
                                        
                                    // Refresh the infolist
                                    $this->dispatch('refresh');
                                }),
                                
                            \Filament\Infolists\Components\Actions\Action::make('update_group_batch')
                                ->label('Update Group for All Resources')
                                ->icon('heroicon-o-folder')
                                ->color('warning')
                                ->form([
                                    Forms\Components\Select::make('new_group_id')
                                        ->label('New Group')
                                        ->options(function () {
                                            return ResourceGroup::orderBy('name')
                                                ->get()
                                                ->mapWithKeys(fn ($group) => [
                                                    $group->id => $group->full_path
                                                ]);
                                        })
                                        ->required()
                                        ->searchable(),
                                ])
                                ->action(function (array $data, $record) {
                                    $oldGroup = $record->group?->full_path ?? 'None';
                                    $record->updateResourcesGroup($data['new_group_id']);
                                    $newGroup = ResourceGroup::find($data['new_group_id'])?->full_path;
                                    
                                    Notification::make()
                                        ->title('Group Updated')
                                        ->body("Group for all {$record->resources()->count()} resources changed from '{$oldGroup}' to '{$newGroup}'")
                                        ->success()
                                        ->send();
                                        
                                    // Refresh the infolist
                                    $this->dispatch('refresh');
                                }),

                            \Filament\Infolists\Components\Actions\Action::make('update_price_batch')
                                ->label('Update Prices for All Resources')
                                ->icon('heroicon-o-currency-dollar')
                                ->color('warning')
                                ->form([
                                    Forms\Components\TextInput::make('new_price')
                                        ->label('New Price')
                                        ->numeric()
                                        ->prefix('Ksh')
                                        ->required()
                                        ->helperText('Set 0 for free resources'),
                                        
                                    Forms\Components\Toggle::make('apply_to_all')
                                        ->label('Apply to all resources')
                                        ->default(true)
                                        ->disabled()
                                        ->helperText('This will update all resources in this bulk upload'),
                                ])
                                ->action(function (array $data, $record) {
                                    $oldPrice = $record->price;
                                    $record->updateResourcesPrice($data['new_price']);
                                    $newPrice = $data['new_price'] == 0 ? 'Free' : 'Ksh ' . number_format($data['new_price'], 0);
                                    
                                    Notification::make()
                                        ->title('Price Updated')
                                        ->body("Price for all {$record->resources()->count()} resources changed from '{$oldPrice}' to '{$newPrice}'")
                                        ->success()
                                        ->send();
                                        
                                    // Refresh the infolist
                                    $this->dispatch('refresh');
                                }),
                                
                            \Filament\Infolists\Components\Actions\Action::make('delete_all_resources')
                                ->label('Delete All Resources')
                                ->icon('heroicon-o-trash')
                                ->color('danger')
                                ->requiresConfirmation()
                                ->modalHeading('Delete All Resources')
                                ->modalDescription(function ($record) {
                                    $count = $record->resources()->count();
                                    $fileSize = self::formatBytes($record->resources()->sum('file_size'));
                                    return "Are you sure you want to delete all {$count} resources associated with this bulk upload?\n\nThis will also delete approximately {$fileSize} of files from storage. This action cannot be undone.";
                                })
                                ->action(function ($record) {
                                    $count = $record->resources()->count();
                                    $record->deleteAllResources();
                                    
                                    Notification::make()
                                        ->title('Resources Deleted')
                                        ->body("Successfully deleted {$count} resources and their files")
                                        ->success()
                                        ->send();
                                        
                                    // Refresh the infolist
                                    $this->dispatch('refresh');
                                }),
                                
                            \Filament\Infolists\Components\Actions\Action::make('view_all_resources')
                                ->label('View All Resources')
                                ->icon('heroicon-o-document-text')
                                ->color('info')
                                ->url(fn ($record): string => 
                                    route('filament.admin.resources.resources.index', ['tableFilters[bulk_upload_id][value]' => $record->id])
                                )
                                ->openUrlInNewTab(),
                        ])
                        ->columnSpanFull()
                        ->extraAttributes(['class' => 'flex flex-wrap gap-2']),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('category.path')
                    ->label('Category')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('group.full_path')
                    ->label('Group')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->formatStateUsing(function ($state) {
                        if (!$state || $state == 0) {
                            return 'Free';
                        }
                        return 'Ksh ' . number_format($state, 0);
                    })
                    ->sortable()
                    ->color(fn ($state) => (!$state || $state == 0) ? 'success' : 'primary')
                    ->weight(fn ($state) => (!$state || $state == 0) ? 'bold' : 'normal'),

                Tables\Columns\TextColumn::make('uploader.name')
                    ->label('Uploaded By')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('total_files')
                    ->label('Files')
                    ->sortable()
                    ->numeric(),
                    
                Tables\Columns\TextColumn::make('successful_uploads')
                    ->label('Success')
                    ->color('success')
                    ->sortable()
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: false),
                    
                Tables\Columns\TextColumn::make('failed_uploads')
                    ->label('Failed')
                    ->color('danger')
                    ->sortable()
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: false),
                    
                Tables\Columns\TextColumn::make('resources_count')
                    ->label('Resources')
                    ->counts('resources')
                    ->sortable()
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: false),
                    
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'processing',
                        'success' => 'completed',
                        'danger' => 'failed',
                    ])
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Uploaded At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ]),
                    
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Category')
                    ->options(function () {
                        return Category::orderBy('name')
                            ->get()
                            ->mapWithKeys(fn ($cat) => [
                                $cat->id => $cat->depth > 0 
                                    ? str_repeat('— ', $cat->depth) . ' ' . $cat->name 
                                    : $cat->name
                            ]);
                    }),
                    
                Tables\Filters\SelectFilter::make('group_id')
                    ->label('Group')
                    ->options(function () {
                        return ResourceGroup::orderBy('name')
                            ->get()
                            ->mapWithKeys(fn ($group) => [
                                $group->id => $group->full_path
                            ]);
                    }),
                    
                Tables\Filters\Filter::make('has_resources')
                    ->label('Has Resources')
                    ->query(fn ($query) => $query->has('resources')),
                    
                Tables\Filters\Filter::make('no_resources')
                    ->label('No Resources')
                    ->query(fn ($query) => $query->doesntHave('resources')),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\Action::make('view_resources')
                        ->label('View Resources')
                        ->icon('heroicon-o-document-text')
                        ->url(fn (BulkUpload $record): string => 
                            route('filament.admin.resources.resources.index', ['tableFilters[bulk_upload_id][value]' => $record->id])
                        ),
                    Tables\Actions\DeleteAction::make()
                        ->before(function (BulkUpload $record) {
                            $count = $record->resources()->count();
                            if ($count > 0) {
                                Notification::make()
                                    ->title('Deleting Resources')
                                    ->body("Deleting {$count} resources and their associated files...")
                                    ->warning()
                                    ->send();
                            }
                            $record->deleteAllResources();
                        }),
                ])
                ->label('Actions')
                ->icon('heroicon-o-chevron-down')
                ->color('primary')
                ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            $totalResources = 0;
                            foreach ($records as $record) {
                                $totalResources += $record->resources()->count();
                                $record->deleteAllResources();
                            }
                            if ($totalResources > 0) {
                                Notification::make()
                                    ->title('Resources Deleted')
                                    ->body("Deleted {$totalResources} resources from {$records->count()} bulk uploads")
                                    ->success()
                                    ->send();
                            }
                        }),
                    Tables\Actions\BulkAction::make('update_category')
                        ->label('Update Category')
                        ->icon('heroicon-o-tag')
                        ->form([
                            Forms\Components\Select::make('new_category_id')
                                ->label('New Category')
                                ->options(function () {
                                    return Category::orderBy('name')
                                        ->get()
                                        ->mapWithKeys(fn ($cat) => [
                                            $cat->id => $cat->depth > 0 
                                                ? str_repeat('— ', $cat->depth) . ' ' . $cat->name 
                                                : $cat->name
                                        ]);
                                })
                                ->required()
                                ->searchable(),
                        ])
                        ->action(function (array $data, $records) {
                            $updatedCount = 0;
                            foreach ($records as $record) {
                                $record->updateResourcesCategory($data['new_category_id']);
                                $updatedCount += $record->resources()->count();
                            }
                            
                            Notification::make()
                                ->title('Category Updated')
                                ->body("Updated category for {$updatedCount} resources across {$records->count()} bulk uploads")
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }
    
    public static function formatBytes($bytes, $precision = 2): string
    {
        if ($bytes === 0) return '0 B';
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
    
    public static function getRelations(): array
    {
        return [
            BulkUploadResource\RelationManagers\ResourcesRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBulkUploads::route('/'),
            'view' => Pages\ViewBulkUpload::route('/{record}'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'processing')->count() ?: null;
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}