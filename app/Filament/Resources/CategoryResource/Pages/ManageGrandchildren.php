<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use App\Models\Category;
use Filament\Actions;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ManageGrandchildren extends ManageRelatedRecords
{
    protected static string $resource = CategoryResource::class;

    protected static string $relationship = 'children';

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';

    public function getTitle(): string
    {
        $record = $this->getRecord();
        return "Grand Categories of: {$record->getFullPathAttribute()}";
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Grand Category Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => 
                                $set('slug', Str::slug($state).'-'.$this->getRecord()->parent->id)
                            ),
                        
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(Category::class, 'slug', ignoreRecord: true),
                        
                        Forms\Components\Textarea::make('short_description')
                            ->maxLength(500)
                            ->rows(2),
                        
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                        
                        Forms\Components\Toggle::make('is_visible')
                            ->default(true)
                            ->label('Visible'),
                        
                        Forms\Components\Toggle::make('is_featured')
                            ->default(false)
                            ->label('Featured'),
                        
                        Forms\Components\FileUpload::make('thumbnail')
                            ->image()
                            ->directory('categories/thumbnails/grandcategories')
                            ->maxSize(2048),
                    ])->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                ImageColumn::make('thumbnail')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(url('/assets/frontend/images/default-category.png'))
                    ->size(25),
                
                TextColumn::make('name')
                    ->label('Grand Category')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                TextColumn::make('short_description')
                    ->label('Description')
                    ->limit(50)
                    ->searchable(),
                
                TextColumn::make('resources_count')
                    ->label('Resources')
                    ->counts('resources')
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
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('visible')
                    ->label('Visible Only')
                    ->toggle()
                    ->query(fn (Builder $query) => $query->where('is_visible', true)),
                
                Tables\Filters\Filter::make('featured')
                    ->label('Featured Only')
                    ->toggle()
                    ->query(fn (Builder $query) => $query->where('is_featured', true)),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('New Grand Category')
                    ->icon('heroicon-o-plus')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['parent_id'] = $this->getRecord()->id;
                        if (empty($data['slug'])) {
                            $data['slug'] = Str::slug($data['name']);
                        }
                        return $data;
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->requiresConfirmation()
                        ->modalDescription(function ($record) {
                            if ($record->resources()->count() > 0) {
                                return 'This grand category has ' . $record->resources()->count() . ' resources. Deleting it will reassign these resources.';
                            }
                            return 'Are you sure you want to delete this grand category?';
                        }),
                    Action::make('move_up')
                        ->label('Move Up')
                        ->icon('heroicon-o-arrow-up')
                        ->color('gray')
                        ->action(fn ($record) => $this->moveGrandcategoryOrder($record, 'up'))
                        ->visible(fn ($record) => $record->sort_order > 0),
                    
                    Action::make('move_down')
                        ->label('Move Down')
                        ->icon('heroicon-o-arrow-down')
                        ->color('gray')
                        ->action(fn ($record) => $this->moveGrandcategoryOrder($record, 'down'))
                ])
                ->icon('heroicon-o-ellipsis-vertical')
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
            ->reorderable('sort_order');
    }

    protected function moveGrandcategoryOrder($record, $direction)
    {
        $currentOrder = $record->sort_order;
        $newOrder = $direction === 'up' ? $currentOrder - 1 : $currentOrder + 1;
        
        $swapWith = Category::where('parent_id', $record->parent_id)
            ->where('sort_order', $newOrder)
            ->first();
        
        if ($swapWith) {
            $swapWith->update(['sort_order' => $currentOrder]);
            $record->update(['sort_order' => $newOrder]);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Back to Subcategories')
                ->icon('heroicon-o-arrow-left')
                ->url(CategoryResource::getUrl('subcategories', ['record' => $this->getRecord()->parent]))
                ->color('gray'),
            
            Actions\EditAction::make()
                ->label('Edit Subcategory')
                ->record($this->getRecord()),
        ];
    }
}