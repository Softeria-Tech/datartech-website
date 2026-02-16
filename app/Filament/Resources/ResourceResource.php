<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResourceResource\Pages;
use App\Models\Category;
use App\Models\Resource;
use App\Models\MembershipPackage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource as FilamentResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Illuminate\Support\Str;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ResourceResource extends FilamentResource
{
    protected static ?string $model = Resource::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationGroup = 'Library Resources';
    
    protected static ?string $navigationLabel = 'Resources';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Resource')
                    ->tabs([
                        // TAB 1: BASIC INFORMATION
                        Tab::make('Basic')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make('Resource Details')
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn (Set $set, ?string $state) => 
                                                $set('slug', Str::slug($state))
                                            ),
                                            
                                        Forms\Components\TextInput::make('slug')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(Resource::class, 'slug', ignoreRecord: true),
                                            
                                        // In your ResourceResource form, replace the old category select with:

                                        Forms\Components\Select::make('category_id')
                                            ->label('Category')
                                            ->relationship('category', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->createOptionForm([
                                                Forms\Components\TextInput::make('name')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(fn ($state, callable $set) => 
                                                        $set('slug', Str::slug($state))
                                                    ),
                                                Forms\Components\TextInput::make('slug')
                                                    ->required()
                                                    ->unique('categories', 'slug'),
                                                Forms\Components\Select::make('parent_id')
                                                    ->label('Parent Category')
                                                    ->relationship('parent', 'name')
                                                    ->searchable()
                                                    ->preload(),
                                            ])
                                            ->createOptionUsing(fn (array $data) => Category::create($data))
                                            ->columnSpan(1),                                       
                                            
                                        Forms\Components\Select::make('language')
                                            ->options([
                                                'en' => 'English',
                                                'es' => 'Spanish',
                                                'fr' => 'French',
                                                'de' => 'German',
                                                'zh' => 'Chinese',
                                                'ar' => 'Arabic',
                                                'hi' => 'Hindi',
                                            ])
                                            ->default('en')
                                            ->required(),
                                            
                                        Forms\Components\TextInput::make('author')
                                            ->maxLength(255),
                                            
                                        Forms\Components\TextInput::make('publisher')
                                            ->maxLength(255),
                                            
                                        Forms\Components\DatePicker::make('published_date'),
                                        
                                        Forms\Components\TextInput::make('version')
                                            ->maxLength(255)
                                            ->placeholder('v1.0, 2024 Edition, etc.'),
                                            
                                        Forms\Components\TextInput::make('page_count')
                                            ->numeric()
                                            ->minValue(1)
                                            ->maxValue(10000),
                                            
                                        Forms\Components\TextInput::make('isbn')
                                            ->maxLength(255)
                                            ->placeholder('For books/magazines'),
                                    ]),
                                    
                                Section::make('Description')
                                    ->schema([
                                        Forms\Components\Textarea::make('short_description')
                                            ->maxLength(500)
                                            ->rows(3)
                                            ->columnSpanFull()
                                            ->helperText('Brief summary for card listings (max 500 chars)'),
                                            
                                        Forms\Components\RichEditor::make('description')
                                            ->required()
                                            ->columnSpanFull()
                                            ->fileAttachmentsDisk('public')
                                            ->fileAttachmentsDirectory('descriptions'),
                                    ]),
                            ]),
                        
                        // TAB 2: PRICING & ACCESS
                        Tab::make('Pricing')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Section::make('Pricing')
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('price')
                                            ->required()
                                            ->numeric()
                                            ->prefix('Ksh ')
                                            ->default(0.00)
                                            ->live()
                                            ->helperText('Regular price for one-time purchase'),
                                            
                                        Forms\Components\TextInput::make('discount_price')
                                            ->numeric()
                                            ->prefix('Ksh ')
                                            ->nullable()
                                            ->helperText('Leave empty for no discount'),
                                            
                                        Forms\Components\DateTimePicker::make('discount_ends_at')
                                            ->native(false)
                                            ->nullable()
                                            ->visible(fn (Get $get): bool => filled($get('discount_price')))
                                            ->required(fn (Get $get): bool => filled($get('discount_price')))
                                            ->helperText('When does this discount expire?'),
                                            
                                        Forms\Components\Toggle::make('requires_subscription')
                                            ->label('Subscription Only')
                                            ->helperText('If enabled, this resource is only available to subscribers')
                                            ->live(),
                                    ]),
                                    
                                Section::make('Subscription Access')
                                    ->visible(fn (Get $get): bool => $get('requires_subscription'))
                                    ->schema([
                                        Forms\Components\CheckboxList::make('membershipPackages')
                                            ->label('Available for Membership Packages')
                                            ->relationship('membershipPackages', 'name')
                                            ->options(MembershipPackage::where('is_active', true)->pluck('name', 'id'))
                                            ->columns(2)
                                            ->helperText('Which subscription tiers can access this resource?'),
                                    ]),
                            ]),
                        
                        // TAB 3: FILES & MEDIA
                        Tab::make('Files')
                            ->icon('heroicon-o-cloud-arrow-up')
                            ->schema([
                                Section::make('Delivery Method')
                                    ->schema([
                                        Forms\Components\Radio::make('delivery_type')
                                            ->options([
                                                'upload' => 'Upload File',
                                                'url' => 'External URL',
                                                'both' => 'Both Upload & URL',
                                            ])
                                            ->default('upload')
                                            ->live()
                                            ->required()
                                            ->columns(3),
                                    ]),
                                    
                                Section::make('File Upload')
                                    ->visible(fn (Get $get): bool => in_array($get('delivery_type'), ['upload', 'both']))
                                    ->schema([
                                        Forms\Components\FileUpload::make('file_path')
                                            ->label('Main File (Full Version)')
                                            ->directory('resources/full')
                                            ->acceptedFileTypes(['application/pdf', 'application/zip', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/epub+zip'])
                                            ->maxSize(10240) // 10MB in KB
                                            ->afterStateUpdated(function ($state, Set $set, $record) {
                                                if ($state) {
                                                    $set('file_name', $state->getClientOriginalName());
                                                    $set('file_size', $state->getSize());
                                                }
                                            })
                                            ->columnSpanFull()
                                            ->helperText('Upload the complete resource file (PDF, ZIP, DOCX, EPUB)'),
                                            
                                        Forms\Components\FileUpload::make('preview_file_path')
                                            ->label('Preview File (Sample)')
                                            ->directory('resources/previews')
                                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                                            ->maxSize(3072) // 3MB
                                            ->columnSpanFull()
                                            ->helperText('Upload a preview version (first few pages, watermark, etc.)'),
                                            
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('file_name')
                                                    ->label('Original Filename')
                                                    ->disabled()
                                                    ->dehydrated(false),
                                                    
                                                Forms\Components\TextInput::make('file_size')
                                                    ->label('File Size')
                                                    ->disabled()
                                                    ->dehydrated(false)
                                                    ->formatStateUsing(fn ($state) => $state ? number_format($state / 1024, 2) . ' KB' : null),
                                            ]),
                                    ]),
                                    
                                Section::make('External URL')
                                    ->visible(fn (Get $get): bool => in_array($get('delivery_type'), ['url', 'both']))
                                    ->schema([
                                        Forms\Components\TextInput::make('external_url')
                                            ->label('Resource URL')
                                            ->url()
                                            ->maxLength(255)
                                            ->columnSpanFull()
                                            ->helperText('External link to the resource (e.g., Google Drive, Dropbox, external site)'),
                                    ]),
                                    
                                Section::make('Cover Images')
                                    ->schema([
                                        Forms\Components\FileUpload::make('thumbnail')
                                            ->label('Thumbnail Image')
                                            ->directory('resources/thumbnails')
                                            ->image()
                                            ->imageEditor()
                                            ->maxSize(5120) // 5MB
                                            ->columnSpanFull()
                                            ->helperText('Small preview image (recommended: 300x400px)'),
                                            
                                        Forms\Components\FileUpload::make('cover_image')
                                            ->label('Cover Image')
                                            ->directory('resources/covers')
                                            ->image()
                                            ->imageEditor()
                                            ->maxSize(10240) // 10MB
                                            ->columnSpanFull()
                                            ->helperText('Full cover image for product page (recommended: 1200x1600px)'),
                                    ]),
                            ]),
                        
                        // TAB 4: METADATA & SEO
                        Tab::make('SEO & Metadata')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Section::make('Search Engine Optimization')
                                    ->schema([
                                        Forms\Components\TextInput::make('meta_title')
                                            ->maxLength(60)
                                            ->helperText('Recommended: 50-60 characters')
                                            ->placeholder(fn (Get $get): string => Str::limit($get('title') ?? 'Resource Title', 60)),
                                            
                                        Forms\Components\Textarea::make('meta_description')
                                            ->maxLength(160)
                                            ->rows(3)
                                            ->helperText('Recommended: 150-160 characters')
                                            ->placeholder(fn (Get $get): string => Str::limit($get('short_description') ?? $get('description') ?? 'Description', 160)),
                                            
                                        Forms\Components\TagsInput::make('meta_keywords')
                                            ->separator(',')
                                            ->placeholder('Add keywords separated by comma'),
                                    ]),
                                    
                                Section::make('Tags & Organization')
                                    ->schema([
                                        Forms\Components\TagsInput::make('tags')
                                            ->separator(',')
                                            ->placeholder('Add tags separated by comma')
                                            ->helperText('e.g., tax-form, 2024, business, pdf'),
                                    ]),
                            ]),
                        
                        // TAB 5: STATUS & VISIBILITY
                        Tab::make('Status')
                            ->icon('heroicon-o-eye')
                            ->schema([
                                Section::make('Publication Status')
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\Toggle::make('is_published')
                                            ->label('Published')
                                            ->helperText('Visible to customers?')
                                            ->default(false),
                                            
                                        Forms\Components\Toggle::make('featured')
                                            ->label('Featured Resource')
                                            ->helperText('Show in featured sections?')
                                            ->default(false),
                                            
                                        Forms\Components\DateTimePicker::make('published_date')
                                            ->native(false)
                                            ->default(now())
                                            ->helperText('When should this be published?'),
                                            
                                        Forms\Components\TextInput::make('sort_order')
                                            ->numeric()
                                            ->default(0)
                                            ->helperText('Lower numbers appear first'),
                                    ]),
                                    
                                Section::make('Statistics')
                                    ->visible(fn ($record) => $record !== null)
                                    ->schema([
                                        Forms\Components\Placeholder::make('download_count')
                                            ->label('Total Downloads')
                                            ->content(fn ($record): string => number_format($record->download_count ?? 0)),
                                            
                                        Forms\Components\Placeholder::make('purchase_count')
                                            ->label('Times Purchased')
                                            ->content(fn ($record): string => number_format($record->orders()->count() ?? 0)),
                                            
                                        Forms\Components\Placeholder::make('revenue')
                                            ->label('Total Revenue')
                                            ->content(fn ($record): string => '$' . number_format($record->orders()->sum('total') ?? 0, 2)),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('Image')
                    ->circular()
                    ->defaultImageUrl(url('/images/default-resource.png')),
                    
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                    
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->url(fn ($record) => $record->category 
                        ? route('filament.admin.resources.categories.view', $record->category) 
                        : null
                    ),
                    
                Tables\Columns\TextColumn::make('price')
                    ->money('Ksh')
                    ->sortable(),
                    
                Tables\Columns\IconColumn::make('requires_subscription')
                    ->label('Sub Only')
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-lock-open'),
                    
                Tables\Columns\TextColumn::make('download_count')
                    ->label('DLs')
                    ->sortable()
                    ->numeric(),
                    
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')
                ->label('Category')
                ->relationship('category', 'name')
                ->searchable()
                ->preload()
                ->options(fn (): array => 
                    \App\Models\Category::orderBy('name')
                        ->get()
                        ->mapWithKeys(fn ($category) => [
                            $category->id => $category->depth > 0 
                                ? str_repeat('— ', $category->depth) . ' ' . $category->name 
                                : $category->name
                        ])
                        ->toArray()
                ),
            
            SelectFilter::make('parent_category')
                ->label('Main Category')
                ->relationship('category.parent', 'name')
                ->searchable()
                ->preload()
                ->options(fn (): array => 
                    \App\Models\Category::whereNull('parent_id')
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray()
                )
                ->query(function (Builder $query, array $data) {
                    $value = $data['value'] ?? null;
                    if (!$value) return $query;
                    
                    return $query->whereHas('category', function ($q) use ($value) {
                        $q->where('parent_id', $value);
                    });
                }),
                    
                TernaryFilter::make('is_published')
                    ->label('Published')
                    ->placeholder('All')
                    ->trueLabel('Published')
                    ->falseLabel('Draft'),
                    
                TernaryFilter::make('requires_subscription')
                    ->label('Access Type')
                    ->placeholder('All')
                    ->trueLabel('Subscription Only')
                    ->falseLabel('One-time Purchase'),
                    
                SelectFilter::make('language')
                    ->options([
                        'en' => 'English',
                        'es' => 'Spanish',
                        'fr' => 'French',
                    ]),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
                ->label('Actions')
                ->icon('heroicon-o-chevron-down')
                ->color('primary')
                ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('toggle_publish')
                        ->label('Toggle Publish Status')
                        ->icon('heroicon-o-eye')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update([
                                    'is_published' => !$record->is_published
                                ]);
                            }
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
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
            'index' => Pages\ListResources::route('/'),
            'create' => Pages\CreateResource::route('/create'),
            'edit' => Pages\EditResource::route('/{record}/edit'),
            'view' => Pages\ViewResource::route('/{record}'),
        ];
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