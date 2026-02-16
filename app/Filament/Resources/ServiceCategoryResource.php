<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Components;
use App\Models\ServiceCategory;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ServiceCategoryResource\Pages;
use App\Filament\Resources\ServiceCategoryResource\RelationManagers;

class ServiceCategoryResource extends Resource
{
    protected static ?string $model = ServiceCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    protected static ?string $navigationGroup = 'Shop';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('category_name')->placeholder('Enter Services Category Name')->required(),
                RichEditor::make('category_desc')
                    ->label('Description')
                    ->placeholder('Enter Description...')
                    ->required(),
                FileUpload::make('image')
                    ->label('Category Image')
                    ->image()
                    ->directory('uploads/service-categories')
                    ->imagePreviewHeight('150')
                    ->maxSize(2048)
                    ->required(),
                FileUpload::make('icon')
                    ->label('Category Icon')
                    ->image()
                    ->directory('uploads/service-categories')
                    ->imagePreviewHeight('150')
                    ->maxSize(1048),
                TextInput::make('service_url')->label('Service URL')->placeholder('Enter Service Request URL')->required(),

                Hidden::make('created_by')
                    ->default(fn() => Auth::user()->id),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('category_name')->label('Categry Name'),
                TextColumn::make('category_desc')->label('Description')->formatStateUsing(fn($state) => Str::limit(strip_tags($state), 40, '...')),
                TextColumn::make('service_url')->label('Service URL'),
                TextColumn::make('creator.name')
                    ->label('Created By'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->actionsColumnLabel('Action')
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
            'index' => Pages\ListServiceCategories::route('/'),
            'create' => Pages\CreateServiceCategory::route('/create'),
            'edit' => Pages\EditServiceCategory::route('/{record}/edit'),
        ];
    }
}
