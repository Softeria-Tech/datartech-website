<?php

namespace App\Filament\Resources\ResourceGroupResource\Pages;

use App\Filament\Resources\ResourceGroupResource;
use App\Models\Resource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;

class ViewResourceGroup extends ViewRecord implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = ResourceGroupResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Resource::query()
                    ->whereIn('group_id', $this->record->getAllDescendantIds())
                    ->with('category')
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->url(fn ($record) => route('filament.admin.resources.resources.view', $record))
                    ->color('primary')
                    ->extraAttributes(['class' => 'cursor-pointer']),
                    
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->formatStateUsing(fn ($state) => $state && $state > 0 ? 'Ksh ' . number_format($state, 0) : 'Free')
                    ->badge()
                    ->color(fn ($state) => $state && $state > 0 ? 'primary' : 'success')
                    ->sortable()
                    ->toggleable()
                    ->alignRight(),
                    
                Tables\Columns\TextColumn::make('download_count')
                    ->label('Downloads')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->toggleable()
                    ->alignCenter(),
                    
                Tables\Columns\IconColumn::make('requires_subscription')
                    ->label('Sub Only')
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-lock-open')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignCenter(),
                    
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable()
                    ->toggleable()
                    ->alignCenter(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Added')
                    ->date('M d, Y')
                    ->sortable()
                    ->toggleable()
                    ->alignRight(),
                    
                Tables\Columns\TextColumn::make('file_size')
                    ->label('Size')
                    ->formatStateUsing(fn ($state) => $state ? self::formatBytes($state) : '-')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignRight(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                    
                Tables\Filters\SelectFilter::make('is_published')
                    ->label('Status')
                    ->options([
                        '1' => 'Published',
                        '0' => 'Draft',
                    ]),
                    
                Tables\Filters\SelectFilter::make('requires_subscription')
                    ->label('Access Type')
                    ->options([
                        '1' => 'Subscription Only',
                        '0' => 'One-time Purchase',
                    ]),
                    
                Tables\Filters\Filter::make('price_range')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('price_from')
                            ->label('Price From')
                            ->numeric()
                            ->prefix('Ksh'),
                        \Filament\Forms\Components\TextInput::make('price_to')
                            ->label('Price To')
                            ->numeric()
                            ->prefix('Ksh'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['price_from'],
                                fn (Builder $query, $price): Builder => $query->where('price', '>=', $price),
                            )
                            ->when(
                                $data['price_to'],
                                fn (Builder $query, $price): Builder => $query->where('price', '<=', $price),
                            );
                    })
                    ->columnSpan(2)
                    ->columns(2),
                    
                Tables\Filters\TernaryFilter::make('featured')
                    ->label('Featured Only'),
                    
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from')
                            ->label('From'),
                        \Filament\Forms\Components\DatePicker::make('created_until')
                            ->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->columns(2),
            ])
            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContent)
            ->persistFiltersInSession()
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('view')
                        ->label('View')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->url(fn ($record) => route('filament.admin.resources.resources.view', $record)),
                        
                    Tables\Actions\Action::make('edit')
                        ->label('Edit')
                        ->icon('heroicon-o-pencil')
                        ->color('warning')
                        ->url(fn ($record) => route('filament.admin.resources.resources.edit', $record)),
                        
                    Tables\Actions\Action::make('toggle_publish')
                        ->label(fn ($record) => $record->is_published ? 'Unpublish' : 'Publish')
                        ->icon(fn ($record) => $record->is_published ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                        ->color(fn ($record) => $record->is_published ? 'warning' : 'success')
                        ->requiresConfirmation()
                        ->action(fn ($record) => $record->update(['is_published' => !$record->is_published])),
                        
                    Tables\Actions\DeleteAction::make(),
                ])
                ->label('Actions')
                ->icon('heroicon-o-chevron-down')
                ->size('sm')
                ->color('gray')
                ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Publish Selected')
                        ->icon('heroicon-o-eye')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['is_published' => true])),
                        
                    Tables\Actions\BulkAction::make('unpublish')
                        ->label('Unpublish Selected')
                        ->icon('heroicon-o-eye-slash')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['is_published' => false])),
                        
                    Tables\Actions\BulkAction::make('set_price')
                        ->label('Set Price')
                        ->icon('heroicon-o-currency-dollar')
                        ->form([
                            \Filament\Forms\Components\TextInput::make('price')
                                ->label('Price (Ksh)')
                                ->numeric()
                                ->required()
                                ->prefix('Ksh'),
                        ])
                        ->action(function ($records, array $data) {
                            $records->each->update(['price' => $data['price']]);
                        }),
                        
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-document')
            ->emptyStateHeading('No resources in this group')
            ->emptyStateDescription('Create resources or upload files to this group.')
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->poll('60s')
            ->paginated([10, 25, 50, 100, 250, 500])
            ->defaultPaginationPageOption(25);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Group Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('name'),
                                TextEntry::make('slug'),
                                TextEntry::make('full_path')
                                    ->label('Full Path'),
                                TextEntry::make('parent.name')
                                    ->label('Parent Group')
                                    ->default('No parent'),
                                TextEntry::make('sort_order'),
                                TextEntry::make('is_active')
                                    ->badge()
                                    ->formatStateUsing(fn (bool $state): string => $state ? 'Active' : 'Inactive')
                                    ->color(fn (bool $state): string => $state ? 'success' : 'danger'),
                            ]),
                    ]),
                    
                Section::make('Description')
                    ->schema([
                        TextEntry::make('description')
                            ->markdown()
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                    
                // Resources Table Section
                Section::make('Resources in this Group')
                    ->description('All resources in this group and its sub-groups')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        \Filament\Infolists\Components\View::make('filament.infolists.components.resources-table')
                            ->viewData([
                                'totalResources' => $this->record->resources()->count(),
                                'totalSubGroups' => $this->record->children()->count(),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(false)
                    ->headerActions([
                        \Filament\Infolists\Components\Actions\Action::make('create_resource')
                            ->label('Create Resource')
                            ->icon('heroicon-o-plus')
                            ->color('success')
                            ->url(route('filament.admin.resources.resources.create', ['group_id' => $this->record->id])),
                            
                        \Filament\Infolists\Components\Actions\Action::make('bulk_upload')
                            ->label('Bulk Upload')
                            ->icon('heroicon-o-cloud-arrow-up')
                            ->color('warning')
                            ->url(ResourceGroupResource::getUrl('bulk-upload', ['record' => $this->record])),
                    ]),
                    
                Section::make('Sub-groups')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('sub-name')
                                    ->label('Name')
                                    ->extraAttributes(['class' => 'font-bold text-gray-700']),
                                TextEntry::make('resources_count')
                                    ->label('Count')
                                    ->extraAttributes(['class' => 'font-bold text-gray-700']),
                                TextEntry::make('children_count')
                                    ->label('Sub Groups')
                                    ->extraAttributes(['class' => 'font-bold text-gray-700']),
                            ])
                            ->visible(fn ($record) => $record->resources()->count() > 0),
                        RepeatableEntry::make('children')
                            ->label('')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('name')
                                            ->label('')
                                            ->url(fn ($record) => ResourceGroupResource::getUrl('view', ['record' => $record]))
                                            ->color('primary')
                                            ->weight('bold'),
                                        TextEntry::make('resources_count')
                                            ->label('')
                                            ->getStateUsing(fn ($record) => $record->resources()->count())
                                            ->numeric()
                                            ->color('info'),
                                        TextEntry::make('children_count')
                                            ->label('')
                                            ->getStateUsing(fn ($record) => $record->children()->count())
                                            ->badge()
                                            ->color('warning'),
                                    ]),
                            ])
                            ->contained(false)
                            ->visible(fn ($record) => $record->children()->count() > 0),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
            Actions\Action::make('bulk_upload')
                ->label('Bulk Upload')
                ->icon('heroicon-o-cloud-arrow-up')
                ->color('success')
                ->url(fn () => ResourceGroupResource::getUrl('bulk-upload', ['record' => $this->record])),
        ];
    }

    protected function getTableQueryStringIdentifier(): string
    {
        return 'resources';
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [10, 25, 50, 100, 250, 500];
    }

    public static function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}