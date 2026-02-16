<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MembershipPackageResource\Pages;
use App\Filament\Resources\MembershipPackageResource\RelationManagers;
use App\Models\MembershipPackage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MembershipPackageResource extends Resource
{
    protected static ?string $model = MembershipPackage::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    protected static ?string $navigationGroup = 'Library Resources';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('features')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('price_monthly')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('price_yearly')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('price_quarterly')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('price_lifetime')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('discount_percentage')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('discount_price_monthly')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('discount_price_yearly')
                    ->numeric()
                    ->default(null),
                Forms\Components\DateTimePicker::make('discount_ends_at'),
                Forms\Components\TextInput::make('duration_days')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('trial_days')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('download_limit_per_month')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('download_limit_per_day')
                    ->numeric()
                    ->default(null),
                Forms\Components\Toggle::make('has_premium_only_access')
                    ->required(),
                Forms\Components\Textarea::make('allowed_categories')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('allows_early_access')
                    ->required(),
                Forms\Components\Toggle::make('is_popular')
                    ->required(),
                Forms\Components\TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                /*Tables\Columns\TextColumn::make('slug')
                    ->searchable(),*/
                Tables\Columns\TextColumn::make('price_monthly')
                    ->numeric()
                    ->label('Monthly')
                    ->sortable(),
                /*Tables\Columns\TextColumn::make('price_yearly')
                    ->numeric()
                    ->label('Yearly')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_quarterly')
                    ->numeric()
                    ->label('Quarterly')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_lifetime')
                    ->numeric()
                    ->label('Lifetime')
                    ->sortable(),*/
                Tables\Columns\TextColumn::make('discount_percentage')
                    ->numeric()
                    ->label('Disc %')
                    ->sortable(),
                /*Tables\Columns\TextColumn::make('discount_price_monthly')
                    ->numeric()
                    ->label('Disc. Monthly')
                    ->sortable(),
                Tables\Columns\TextColumn::make('discount_price_yearly')
                    ->numeric()
                    ->label('Disc. Yr')
                    ->sortable(),
                Tables\Columns\TextColumn::make('discount_ends_at')
                    ->dateTime()
                    ->label('Disc. Ends')
                    ->sortable(),*/
                Tables\Columns\TextColumn::make('duration_days')
                    ->numeric()
                    ->label('Days')
                    ->sortable(),
                /*Tables\Columns\TextColumn::make('trial_days')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('download_limit_per_month')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('download_limit_per_day')
                    ->numeric()
                    ->sortable(),*/
                Tables\Columns\IconColumn::make('has_premium_only_access')
                    ->label('Premium Only')
                    ->boolean(),
                Tables\Columns\IconColumn::make('allows_early_access')
                    ->label('Early Access')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_popular')
                    ->boolean(),
                /*Tables\Columns\TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),*/
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                /*Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),*/
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
            'index' => Pages\ListMembershipPackages::route('/'),
            'create' => Pages\CreateMembershipPackage::route('/create'),
            'view' => Pages\ViewMembershipPackage::route('/{record}'),
            'edit' => Pages\EditMembershipPackage::route('/{record}/edit'),
        ];
    }
}
