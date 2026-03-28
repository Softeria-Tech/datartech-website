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
use Filament\Notifications\Notification;

class BulkUploadResource extends Resource
{
    protected static ?string $model = BulkUpload::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-cloud-arrow-up';
    
    protected static ?string $navigationGroup = 'Content Management';
    
    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Bulk Upload Information')
                    ->schema([
                        Forms\Components\TextInput::make('category.full_path')
                            ->label('Current Category')
                            ->disabled(),
                            
                        Forms\Components\TextInput::make('group.path')
                            ->label('Current Group')
                            ->disabled(),
                            
                        Forms\Components\TextInput::make('originalCategory.full_path')
                            ->label('Original Category')
                            ->disabled(),
                            
                        Forms\Components\TextInput::make('originalGroup.path')
                            ->label('Original Group')
                            ->disabled(),

                        Forms\Components\TextInput::make('price')
                            ->label('Price')
                            ->numeric()
                            ->disabled(),
                                                    
                        Forms\Components\TextInput::make('total_files')
                            ->label('Total Files')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false),
                            
                        Forms\Components\TextInput::make('successful_uploads')
                            ->label('Successful Uploads')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false),
                            
                        Forms\Components\TextInput::make('failed_uploads')
                            ->label('Failed Uploads')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false),
                            
                        Forms\Components\TextInput::make('status')
                            ->disabled()
                            ->default('pending'),
                            
                        Forms\Components\DateTimePicker::make('completed_at')
                            ->label('Completed At')
                            ->nullable(),
                            
                        Forms\Components\KeyValue::make('metadata')
                            ->label('Metadata')
                            ->columnSpanFull()
                            ->nullable(),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Statistics')
                    ->schema([
                        Forms\Components\Placeholder::make('total_resources')
                            ->label('Total Resources Created')
                            ->content(fn ($record): string => $record ? number_format($record->resources()->count()) : '0'),
                            
                        Forms\Components\Placeholder::make('total_file_size')
                            ->label('Total File Size')
                            ->content(fn ($record): string => $record ? self::formatBytes($record->resources()->sum('file_size')) : '0 B'),
                    ])->columns(2)
                    ->visible(fn ($record) => $record !== null),
                    
                Forms\Components\Section::make('Batch Actions')
                    ->schema([
                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('update_category_batch')
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
                                ->action(function (array $data, BulkUpload $record) {
                                    $oldCategory = $record->category?->path ?? 'None';
                                    $record->updateResourcesCategory($data['new_category_id']);
                                    $newCategory = Category::find($data['new_category_id'])?->path;
                                    
                                    Notification::make()
                                        ->title('Category Updated')
                                        ->body("Category for all {$record->resources()->count()} resources changed from '{$oldCategory}' to '{$newCategory}'")
                                        ->success()
                                        ->send();
                                }),
                                
                            Forms\Components\Actions\Action::make('update_group_batch')
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
                                ->action(function (array $data, BulkUpload $record) {
                                    $oldGroup = $record->group?->full_path ?? 'None';
                                    $record->updateResourcesGroup($data['new_group_id']);
                                    $newGroup = ResourceGroup::find($data['new_group_id'])?->full_path;
                                    
                                    Notification::make()
                                        ->title('Group Updated')
                                        ->body("Group for all {$record->resources()->count()} resources changed from '{$oldGroup}' to '{$newGroup}'")
                                        ->success()
                                        ->send();
                                }),

                            Forms\Components\Actions\Action::make('update_price_batch')
                                ->label('Update prices for All Resources')
                                ->icon('heroicon-o-folder')
                                ->color('warning')
                                ->form([
                                    Forms\Components\TextInput::make('new_price')
                                        ->label('New Price')
                                        ->required(),
                                ])
                                ->action(function (array $data, BulkUpload $record) {
                                    $oldPrice = $record->price;
                                    $record->updateResourcesPrice($data['new_price']);
                                    $newPrice = $data['new_price'];
                                    Notification::make()
                                        ->title('Price Updated')
                                        ->body("Price for all {$record->resources()->count()} resources changed from '{$oldPrice}' to '{$newPrice}'")
                                        ->success()
                                        ->send();
                                }),
                                
                            Forms\Components\Actions\Action::make('delete_all_resources')
                                ->label('Delete All Resources')
                                ->icon('heroicon-o-trash')
                                ->color('danger')
                                ->requiresConfirmation()
                                ->action(function (BulkUpload $record) {
                                    $count = $record->resources()->count();
                                    $record->deleteAllResources();
                                    
                                    Notification::make()
                                        ->title('Resources Deleted')
                                        ->body("Successfully deleted {$count} resources and their files")
                                        ->success()
                                        ->send();
                                }),
                        ]),
                    ])->visible(fn ($record) => $record !== null),
            ]);
    }
    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('group.full_path')
                    ->label('Group')
                    ->sortable()
                    ->searchable(),

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
                    ->numeric(),
                    
                Tables\Columns\TextColumn::make('failed_uploads')
                    ->label('Failed')
                    ->color('danger')
                    ->sortable()
                    ->numeric(),
                    
                Tables\Columns\TextColumn::make('resources_count')
                    ->label('Resources')
                    ->counts('resources')
                    ->sortable()
                    ->numeric()->toggleable(),
                    
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'processing',
                        'success' => 'completed',
                        'danger' => 'failed',
                    ]),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Uploaded At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable(),
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
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('view_resources')
                        ->label('View Resources')
                        ->icon('heroicon-o-document-text')
                        ->url(fn (BulkUpload $record): string => 
                            route('filament.admin.resources.resources.index', ['tableFilters[bulk_upload_id][value]' => $record->id])
                        ),
                    Tables\Actions\DeleteAction::make()
                        ->before(function (BulkUpload $record) {
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
                            foreach ($records as $record) {
                                $record->deleteAllResources();
                            }
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'edit' => Pages\EditBulkUpload::route('/{record}/edit'),
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