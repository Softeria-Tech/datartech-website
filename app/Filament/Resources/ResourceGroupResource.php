<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResourceGroupResource\Pages;
use App\Models\ResourceGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ResourceGroupResource extends Resource
{
    protected static ?string $model = ResourceGroup::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationGroup = 'Library Resources';

    protected static ?string $navigationLabel = 'Resource Groups';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Group')
                    ->tabs([
                        Tab::make('Basic')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => 
                                                $set('slug', Str::slug($state).'-'.uniqid())
                                            ),
                                            
                                        Forms\Components\TextInput::make('slug')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(ResourceGroup::class, 'slug', ignoreRecord: true),
                                            
                                        Forms\Components\Select::make('parent_id')
                                            ->label('Parent Group')
                                            ->searchable()
                                            ->preload()
                                            ->options(function () {
                                                return ResourceGroup::orderBy('name')
                                                    ->get()
                                                    ->mapWithKeys(fn ($group) => [
                                                        $group->id => $group->full_path
                                                    ]);
                                            })
                                            ->placeholder('Select parent group (optional)'),
                                            
                                        Forms\Components\TextInput::make('sort_order')
                                            ->numeric()
                                            ->default(0)
                                            ->helperText('Lower numbers appear first'),
                                            
                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Active')
                                            ->default(true)
                                            ->helperText('Inactive groups are hidden from the frontend'),
                                    ]),
                                    
                                Forms\Components\Textarea::make('description')
                                    ->rows(3)
                                    ->columnSpanFull(),
                                    
                                Forms\Components\FileUpload::make('cover_image')
                                    ->label('Cover Image')
                                    ->directory('resource-groups/covers')
                                    ->image()
                                    ->imageEditor()
                                    ->maxSize(5120)
                                    ->columnSpanFull(),
                            ]),
                            
                        Tab::make('SEO')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Forms\Components\TextInput::make('meta_title')
                                    ->maxLength(60)
                                    ->helperText('Recommended: 50-60 characters'),
                                    
                                Forms\Components\Textarea::make('meta_description')
                                    ->maxLength(160)
                                    ->rows(3)
                                    ->helperText('Recommended: 150-160 characters'),
                            ]),
                    ])
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl(url('/assets/frontend/images/default-folder.png')),
                    
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    /*->formatStateUsing(fn ($state, $record) => 
                        str_repeat('— ', $record->depth) . ' ' . $state
                    )*/,
                    
                Tables\Columns\TextColumn::make('level_name')
                    ->label('Level')
                    ->badge()
                    ->color(fn ($record) => match($record->depth) {
                        0 => 'success',
                        1 => 'info',
                        2 => 'warning',
                        3 => 'danger',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('full_path')
                    ->label('Path')
                    ->color('gray')
                    ->size('sm'),
                    
                Tables\Columns\TextColumn::make('resources_count')
                    ->label('Resources')
                    ->counts('resources')
                    ->badge()
                    ->color('info'),
                    
                Tables\Columns\TextColumn::make('children_count')
                    ->label('Sub-groups')
                    ->counts('children')
                    ->badge()
                    ->color('warning'),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('parent_id')
                    ->label('Parent Group')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
                    
                Tables\Filters\SelectFilter::make('depth')
                    ->label('Level')
                    ->options([
                        0 => 'Parent Groups',
                        1 => 'Sub Groups',
                        2 => 'Grand Groups',
                        3 => '4th Degree Groups',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['value'] !== null) {
                            $depth = (int) $data['value'];
                            if ($depth === 0) {
                                $query->whereNull('parent_id');
                            } elseif ($depth === 1) {
                                $query->whereNotNull('parent_id')
                                    ->whereDoesntHave('parent.parent');
                            } elseif ($depth === 2) {
                                $query->whereHas('parent', function ($q) {
                                    $q->whereNotNull('parent_id')
                                        ->whereDoesntHave('parent.parent');
                                });
                            } elseif ($depth === 3) {
                                $query->whereHas('parent.parent', function ($q) {
                                    $q->whereNotNull('parent_id')
                                        ->whereDoesntHave('parent.parent');
                                });
                            }
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\Action::make('bulk_upload')
                        ->label('Bulk Upload to Group')
                        ->icon('heroicon-o-cloud-arrow-up')
                        ->color('success')
                        ->url(fn ($record) => route('filament.admin.resources.resource-groups.bulk-upload', $record)),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('toggle_active')
                        ->label('Toggle Active Status')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn ($records) => $records->each->update([
                            'is_active' => !$records->first()->is_active
                        ])),
                ]),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResourceGroups::route('/'),
            'create' => Pages\CreateResourceGroup::route('/create'),
            'edit' => Pages\EditResourceGroup::route('/{record}/edit'),
            'view' => Pages\ViewResourceGroup::route('/{record}'),
            'bulk-upload' => Pages\BulkUploadResources::route('/{record}/bulk-upload'),
        ];
    }
}