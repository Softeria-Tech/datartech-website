<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserDownloadResource\Pages;
use App\Models\UserDownload;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class UserDownloadResource extends Resource
{
    protected static ?string $model = UserDownload::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray';
    
    protected static ?string $navigationGroup = 'Library Resources';
    
    protected static ?string $navigationLabel = 'Downloads';
    
    protected static ?string $pluralModelLabel = 'Downloads';
    
    protected static ?int $navigationSort = 4;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('downloaded_at')
                    ->label('Downloaded')
                    ->dateTime('M d, Y - g:i A')
                    ->sortable()
                    ->searchable()
                    ->weight('bold')
                    ->color('primary'),
                
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->user?->email),
                
                TextColumn::make('resource.title')
                    ->label('Resource')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->url(fn ($record) => $record->resource 
                        ? route('filament.admin.resources.resources.view', $record->resource) 
                        : null
                    )
                    ->color('primary'),
                
                TextColumn::make('access_type')
                    ->label('Access')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'one_time_purchase' => 'Purchase',
                        'subscription' => 'Subscription',
                        'free' => 'Free',
                        'admin_grant' => 'Admin',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match($state) {
                        'one_time_purchase' => 'success',
                        'subscription' => 'info',
                        'free' => 'warning',
                        'admin_grant' => 'gray',
                        default => 'gray',
                    }),
                
                TextColumn::make('amount_paid')
                    ->label('Amount')
                    ->money('Ksh')
                    ->sortable()
                    ->visible(fn () => class_exists('App\\Models\\Order')),
                
                TextColumn::make('download_count')
                    ->label('Count')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color(fn (int $state): string => $state > 1 ? 'warning' : 'gray'),
                
                TextColumn::make('ip_address')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('access_type')
                    ->label('Access Method')
                    ->options([
                        'one_time_purchase' => 'One-time Purchase',
                        'subscription' => 'Subscription',
                        'free' => 'Free Download',
                        'admin_grant' => 'Admin Granted',
                    ]),
                
                Tables\Filters\Filter::make('downloaded_at')
                    ->form([
                        Forms\Components\DatePicker::make('downloaded_from')
                            ->label('From'),
                        Forms\Components\DatePicker::make('downloaded_until')
                            ->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['downloaded_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('downloaded_at', '>=', $date),
                            )
                            ->when(
                                $data['downloaded_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('downloaded_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('View'),
            ])
            ->bulkActions([])
            ->defaultSort('downloaded_at', 'desc')
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserDownloads::route('/'),
            'view' => Pages\ViewUserDownload::route('/{record}'),
        ];
    }
}