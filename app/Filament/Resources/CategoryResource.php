<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    protected static ?string $navigationGroup = 'Library Resources';
    
    protected static ?string $navigationLabel = 'Categories';
    
    protected static ?string $pluralModelLabel = 'Categories';
    
    protected static ?int $navigationSort = 2;
    
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Category Information')
                    ->description('Basic category details')
                    ->icon('heroicon-o-tag')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Category Name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => 
                                $set('slug', Str::slug($state))
                            )
                            ->columnSpan(1),
                        
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(Category::class, 'slug', ignoreRecord: true)
                            ->columnSpan(1),
                        
                        Select::make('parent_id')
                            ->label('Parent Category')
                            ->placeholder('None (Parent)')
                            ->relationship('parent', 'name', fn (Builder $query) => 
                                $query->whereNull('parent_id')->orderBy('name')
                            )
                            ->searchable()
                            ->preload()
                            ->columnSpan(1)
                            ->helperText('Leave empty to create a main category'),
                        
                        TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('Lower numbers appear first')
                            ->columnSpan(1),
                        
                        Grid::make(2)
                            ->schema([
                                Toggle::make('is_visible')
                                    ->label('Visible')
                                    ->default(true)
                                    ->helperText('Show this category on the site'),
                                
                                Toggle::make('is_featured')
                                    ->label('Featured')
                                    ->default(false)
                                    ->helperText('Show in featured sections'),
                            ]),
                        
                        TextInput::make('icon')
                            ->label('Icon')
                            ->placeholder('heroicon-o-folder, fa-folder, etc.')
                            ->maxLength(255)
                            ->columnSpan(1)
                            ->helperText('Font Awesome, Heroicons, or custom CSS class'),
                    ]),
                
                Section::make('Description')
                    ->schema([
                        Textarea::make('short_description')
                            ->label('Short Description')
                            ->rows(2)
                            ->maxLength(500)
                            ->columnSpanFull()
                            ->helperText('Brief summary for category listings'),
                        
                        RichEditor::make('description')
                            ->label('Full Description')
                            ->columnSpanFull()
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('categories/descriptions'),
                    ]),
                
                Section::make('Images')
                    ->description('Category visuals')
                    ->icon('heroicon-o-photo')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('thumbnail')
                            ->label('Thumbnail')
                            ->directory('categories/thumbnails')
                            ->image()
                            ->imageEditor()
                            ->maxSize(2048)
                            ->helperText('Square image recommended (300x300px)')
                            ->columnSpan(1),
                        
                        FileUpload::make('cover_image')
                            ->label('Cover Image')
                            ->directory('categories/covers')
                            ->image()
                            ->imageEditor()
                            ->maxSize(5120)
                            ->helperText('Wide banner image (1200x400px)')
                            ->columnSpan(1),
                    ]),
                
                Section::make('SEO')
                    ->description('Search engine optimization')
                    ->icon('heroicon-o-magnifying-glass')
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('Meta Title')
                            ->maxLength(60)
                            ->placeholder(fn ($get) => Str::limit($get('name') ?? 'Category', 60))
                            ->helperText('Recommended: 50-60 characters'),
                        
                        Textarea::make('meta_description')
                            ->label('Meta Description')
                            ->rows(2)
                            ->maxLength(160)
                            ->helperText('Recommended: 150-160 characters'),
                        
                        TextInput::make('meta_keywords')
                            ->label('Meta Keywords')
                            ->maxLength(255)
                            ->helperText('Comma-separated keywords'),
                    ]),
                
                Section::make('Settings')
                    ->description('Additional configuration')
                    ->icon('heroicon-o-cog')
                    ->schema([
                        Forms\Components\KeyValue::make('settings')
                            ->label('Custom Settings')
                            ->columnSpanFull()
                            ->helperText('Additional category settings (optional)'),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(url('/assets/frontend/images/default-category.png'))
                    ->size(40),
                
                TextColumn::make('name')
                    ->label('Category')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->path)
                    ->wrap(),
                
                TextColumn::make('parent.name')
                    ->label('Parent')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Parent')
                    ->badge()
                    ->color('gray')
                    ->toggleable(),
                
                TextColumn::make('children_count')
                    ->label('Sub-categories')
                    ->counts('children')
                    ->badge()
                    ->color('info')
                    ->alignCenter()
                    ->formatStateUsing(fn ($state) => $state > 0 ? $state : '-'),
                
                TextColumn::make('resources_count')
                    ->label('Resources')
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->alignCenter(),
                
                IconColumn::make('is_visible')
                    ->label('Visible')
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye-slash')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable()
                    ->alignCenter(),
                
                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->alignCenter(),
                
                TextColumn::make('sort_order')
                    ->label('Order')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('is_root')
                    ->label('Root Categories')
                    ->toggle()
                    ->query(fn (Builder $query) => $query->whereNull('parent_id')),
                
                Filter::make('has_children')
                    ->label('Has Sub-categories')
                    ->toggle()
                    ->query(fn (Builder $query) => $query->has('children')),
                
                SelectFilter::make('parent_id')
                    ->label('Parent Category')
                    ->relationship('parent', 'name', fn (Builder $query) => 
                        $query->whereNull('parent_id')->orderBy('name')
                    )
                    ->searchable()
                    ->preload(),
                
                Filter::make('visible')
                    ->label('Visible Only')
                    ->toggle()
                    ->query(fn (Builder $query) => $query->where('is_visible', true)),
                
                Filter::make('featured')
                    ->label('Featured Only')
                    ->toggle()
                    ->query(fn (Builder $query) => $query->where('is_featured', true)),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Delete Category')
                        ->modalDescription(fn ($record) => 
                            $record->hasChildren() 
                                ? 'This category has sub-categories. Deleting it will detach all sub-categories.' 
                                : 'Are you sure you want to delete this category?'
                        ),
                    Tables\Actions\Action::make('move_up')
                        ->label('Move Up')
                        ->icon('heroicon-o-arrow-up')
                        ->color('gray')
                        ->action(fn ($record) => static::moveOrder($record, 'up'))
                        ->visible(fn ($record) => $record->sort_order > 0),
                    
                    Tables\Actions\Action::make('move_down')
                        ->label('Move Down')
                        ->icon('heroicon-o-arrow-down')
                        ->color('gray')
                        ->action(fn ($record) => static::moveOrder($record, 'down'))
                ])
                ->label('Actions')
                ->icon('heroicon-o-chevron-down')
                ->color('primary')
                ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('toggle_visibility')
                        ->label('Toggle Visibility')
                        ->icon('heroicon-o-eye')
                        ->action(fn ($records) => $records->each->update([
                            'is_visible' => !$records->first()->is_visible
                        ])),
                ]),
            ])
            ->defaultSort('sort_order', 'asc')
            ->reorderable('sort_order')
            ->striped();
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'view' => Pages\ViewCategory::route('/{record}'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }

    protected static function moveOrder($record, $direction)
    {
        $currentOrder = $record->sort_order;
        $newOrder = $direction === 'up' ? $currentOrder - 1 : $currentOrder + 1;
        
        // Find the category to swap with
        $swapWith = Category::where('parent_id', $record->parent_id)
            ->where('sort_order', $newOrder)
            ->first();
        
        if ($swapWith) {
            $swapWith->update(['sort_order' => $currentOrder]);
            $record->update(['sort_order' => $newOrder]);
        }
    }
}