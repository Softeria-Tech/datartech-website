<?php
// app/Filament/Resources/UserDownloadResource/Pages/ViewUserDownload.php

namespace App\Filament\Resources\UserDownloadResource\Pages;

use App\Filament\Resources\UserDownloadResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;

class ViewUserDownload extends ViewRecord
{
    protected static string $resource = UserDownloadResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Download Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('downloaded_at')
                                    ->label('Downloaded On')
                                    ->dateTime('F j, Y - g:i:s A')
                                    ->weight('bold')
                                    ->color('primary'),
                                    
                                TextEntry::make('download_count')
                                    ->label('Times Downloaded')
                                    ->badge()
                                    ->color(fn ($state) => $state > 1 ? 'warning' : 'success'),
                                    
                                TextEntry::make('access_type')
                                    ->label('Access Method')
                                    ->badge()
                                    ->formatStateUsing(fn (string $state): string => match($state) {
                                        'one_time_purchase' => 'One-time Purchase',
                                        'subscription' => 'Subscription',
                                        'free' => 'Free Download',
                                        'admin_grant' => 'Admin Granted',
                                        default => $state,
                                    }),
                                    
                                TextEntry::make('amount_paid')
                                    ->label('Amount Paid')
                                    ->money('USD')
                                    ->visible(fn ($record) => $record->amount_paid > 0),
                                    
                                TextEntry::make('ip_address')
                                    ->label('IP Address')
                                    ->copyable()
                                    ->copyMessage('IP copied'),
                                    
                                TextEntry::make('user_agent')
                                    ->label('Device / Browser')
                                    ->limit(50)
                                    ->tooltip(fn ($record) => $record->user_agent)
                                    ->toggleable(),
                            ]),
                    ]),
                
                Section::make('Customer Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Name')
                                    ->weight('bold')
                                    ->size(TextEntry\TextEntrySize::Large),
                                    
                                TextEntry::make('user.email')
                                    ->label('Email')
                                    ->copyable()
                                    ->copyMessage('Email copied'),
                                    
                                TextEntry::make('user.created_at')
                                    ->label('Customer Since')
                                    ->date('M d, Y'),
                            ]),
                    ]),
                
                Section::make('Resource Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('resource.title')
                                    ->label('Resource Title')
                                    ->weight('bold')
                                    ->url(fn ($record) => $record->resource 
                                        ? route('filament.admin.resources.resources.view', $record->resource) 
                                        : null
                                    )
                                    ->color('primary'),
                                    
                                TextEntry::make('resource.price')
                                    ->label('Regular Price')
                                    ->money('USD'),
                                    
                                TextEntry::make('resource.category')
                                    ->label('Category')
                                    ->badge()
                                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state ?? ''))),
                                    
                                TextEntry::make('resource.file_name')
                                    ->label('Filename')
                                    ->badge()
                                    ->color('info'),
                            ]),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Back to Downloads')
                ->icon('heroicon-o-arrow-left')
                ->url(static::getResource()::getUrl('index'))
                ->color('gray'),
        ];
    }
}