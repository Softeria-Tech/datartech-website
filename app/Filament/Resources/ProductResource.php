<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProductResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductResource\RelationManagers;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    protected static ?string $navigationGroup = 'Shop';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('product_category_id')
                    ->label('Product Category')
                    ->required()
                    ->relationship(name: 'ProductCategory', titleAttribute: 'category_name')
                    ->searchable()
                    ->preload(),
                TextInput::make('product_name')->placeholder('Enter Product Name')->required()->label('Product Name'),
                TextInput::make('product_cost')
                    ->placeholder('Enter Product Cost')
                    ->required()
                    ->label('Product Cost')
                    ->rule('regex:/^\d+(\.\d{1,2})?$/') // Only numeric with optional decimal
                    ->type('text')
                    ->maxLength(10),
                FileUpload::make('product_image')
                    ->label('Product Image')
                    ->image()
                    ->required()
                    ->directory('uploads/products')
                    ->imagePreviewHeight('150')
                    ->maxSize(1048),
                RichEditor::make('product_desc')
                    ->label('Product Description')
                    ->required()
                    ->placeholder('Enter  Specifications...'),
                RichEditor::make('product_specs')
                    ->label('Product Specifications')
                    ->placeholder('Enter  Description...'),

                Hidden::make('created_by')
                    ->default(fn() => Auth::user()->id),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product_name')->label('Product Name'),
                TextColumn::make('productCategory.category_name')->label('Product Category'),
                TextColumn::make('product_desc')->label('Description')->formatStateUsing(fn($state) => Str::limit(strip_tags($state), 20, '...')),
                TextColumn::make('product_cost')->label('Product Cost')->alignRight()->formatStateUsing(fn($state) => number_format($state, 2)),
                TextColumn::make('creator.name')
                    ->label('Created By'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
