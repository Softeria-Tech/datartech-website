<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Service;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ServiceResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ServiceResource\RelationManagers;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    protected static ?string $navigationGroup = 'Shop';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Select::make('service_category_id')
                    ->label('Service Category')
                    ->required()
                    ->relationship(name: 'ServiceCategory', titleAttribute: 'category_name')
                    ->searchable()
                    ->preload(),
                TextInput::make('service_name')->placeholder('Enter ServicesName')->required(),
                Textarea::make('service_desc')
                    ->label('Service Description')
                    ->rows(4)
                    ->placeholder('Enter  Description...'),

                Hidden::make('created_by')
                    ->default(fn() => Auth::user()->id),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('service_name')->label('Service Name'),
                TextColumn::make('serviceCategory.category_name')->label('Service Category'),
                TextColumn::make('service_desc')->label('Description'),
                TextColumn::make('creator.name') // Join with users table to display name
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
            'index' =>  Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
