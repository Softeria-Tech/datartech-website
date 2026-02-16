<?php
// app/Filament/Resources/MembershipPackageResource/Pages/ViewMembershipPackage.php

namespace App\Filament\Resources\MembershipPackageResource\Pages;

use App\Filament\Resources\MembershipPackageResource;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\HtmlString;

class ViewMembershipPackage extends ViewRecord
{
    protected static string $resource = MembershipPackageResource::class;

    public function getTitle(): string
    {
        return "View Package: {$this->record->name}";
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil'),
            Actions\Action::make('preview_frontend')
                ->label('Preview on Site')
                ->icon('heroicon-o-eye')
                ->url(route('membership.plans'), shouldOpenInNewTab: true),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Package Header with Status
                Components\Section::make()
                    ->schema([
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('name')
                                    ->label('Package Name')
                                    ->size(Components\TextEntry\TextEntrySize::Large)
                                    ->weight(FontWeight::Bold)
                                    ->columnSpan(1),
                                    
                                Components\TextEntry::make('slug')
                                    ->label('Slug')
                                    ->badge()
                                    ->color('gray')
                                    ->copyable()
                                    ->copyMessage('Slug copied!')
                                    ->columnSpan(1),
                                    
                                Components\Grid::make(2)
                                    ->schema([
                                        Components\IconEntry::make('is_active')
                                            ->label('Active')
                                            ->boolean()
                                            ->trueIcon('heroicon-o-check-circle')
                                            ->falseIcon('heroicon-o-x-circle')
                                            ->trueColor('success')
                                            ->falseColor('danger'),
                                            
                                        Components\IconEntry::make('is_popular')
                                            ->label('Popular')
                                            ->boolean()
                                            ->trueIcon('heroicon-o-star')
                                            ->falseIcon('heroicon-o-star')
                                            ->trueColor('warning')
                                            ->falseColor('gray'),
                                    ])
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->compact(),
                
                // Description Section
                Components\Section::make('Description')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Components\TextEntry::make('description')
                            ->markdown()
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                
                // Pricing Section
                Components\Section::make('Pricing')
                    ->icon('heroicon-o-currency-dollar')
                    ->schema([
                        Components\Grid::make(4)
                            ->schema([
                                Components\Card::make()
                                    ->schema([
                                        Components\TextEntry::make('price_monthly')
                                            ->label('Monthly')
                                            ->formatStateUsing(fn ($state) => $state ? 'KES ' . number_format($state, 0) : '—')
                                            ->color(fn ($state) => $state ? 'primary' : 'gray')
                                            ->weight(FontWeight::Bold)
                                            ->size(Components\TextEntry\TextEntrySize::Large)
                                            ->alignCenter(),
                                    ])
                                    ->extraAttributes(['class' => 'text-center']),
                                    
                                Components\Card::make()
                                    ->schema([
                                        Components\TextEntry::make('price_quarterly')
                                            ->label('Quarterly')
                                            ->formatStateUsing(fn ($state) => $state ? 'KES ' . number_format($state, 0) : '—')
                                            ->color(fn ($state) => $state ? 'primary' : 'gray')
                                            ->weight(FontWeight::Bold)
                                            ->size(Components\TextEntry\TextEntrySize::Large)
                                            ->alignCenter(),
                                    ])
                                    ->extraAttributes(['class' => 'text-center']),
                                    
                                Components\Card::make()
                                    ->schema([
                                        Components\TextEntry::make('price_yearly')
                                            ->label('Yearly')
                                            ->formatStateUsing(fn ($state) => $state ? 'KES ' . number_format($state, 0) : '—')
                                            ->color(fn ($state) => $state ? 'primary' : 'gray')
                                            ->weight(FontWeight::Bold)
                                            ->size(Components\TextEntry\TextEntrySize::Large)
                                            ->alignCenter(),
                                    ])
                                    ->extraAttributes(['class' => 'text-center']),
                                    
                                Components\Card::make()
                                    ->schema([
                                        Components\TextEntry::make('price_lifetime')
                                            ->label('Lifetime')
                                            ->formatStateUsing(fn ($state) => $state ? 'KES ' . number_format($state, 0) : '—')
                                            ->color(fn ($state) => $state ? 'success' : 'gray')
                                            ->weight(FontWeight::Bold)
                                            ->size(Components\TextEntry\TextEntrySize::Large)
                                            ->alignCenter(),
                                    ])
                                    ->extraAttributes(['class' => 'text-center']),
                            ]),
                            
                        // Discount Information
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('discount_percentage')
                                    ->label('Discount %')
                                    ->formatStateUsing(fn ($state) => $state ? "{$state}% OFF" : 'No discount')
                                    ->badge()
                                    ->color(fn ($state) => $state ? 'success' : 'gray')
                                    ->visible(fn ($state) => !is_null($state)),
                                    
                                Components\TextEntry::make('discount_price_monthly')
                                    ->label('Discounted Monthly')
                                    ->formatStateUsing(fn ($state) => $state ? 'KES ' . number_format($state, 0) : '—')
                                    ->visible(fn ($state) => !is_null($state)),
                                    
                                Components\TextEntry::make('discount_price_yearly')
                                    ->label('Discounted Yearly')
                                    ->formatStateUsing(fn ($state) => $state ? 'KES ' . number_format($state, 0) : '—')
                                    ->visible(fn ($state) => !is_null($state)),
                                    
                                Components\TextEntry::make('discount_ends_at')
                                    ->label('Discount Ends')
                                    ->dateTime('M d, Y H:i')
                                    ->badge()
                                    ->color(fn ($state) => $state && now()->lt($state) ? 'warning' : 'danger')
                                    ->visible(fn ($state) => !is_null($state)),
                            ])
                            ->columns(4)
                            ->visible(fn ($record) => $record->discount_percentage > 0),
                    ])
                    ->collapsible(),
                
                // Features Section
                Components\Section::make('Features')
                    ->icon('heroicon-o-list-bullet')
                    ->schema([
                        Components\TextEntry::make('features')
                            ->formatStateUsing(function ($state) {
                                if (empty($state)) {
                                    return new HtmlString('<p class="text-gray-400">No features listed</p>');
                                }
                                
                                $features = is_array($state) ? $state : json_decode($state, true);
                                
                                if (empty($features)) {
                                    return new HtmlString('<p class="text-gray-400">No features listed</p>');
                                }
                                
                                $html = '<ul class="list-disc list-inside space-y-1">';
                                foreach ($features as $feature) {
                                    $html .= '<li class="text-gray-700">' . e($feature) . '</li>';
                                }
                                $html .= '</ul>';
                                
                                return new HtmlString($html);
                            })
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                
                // Access & Limits Section
                Components\Section::make('Access & Limits')
                    ->icon('heroicon-o-lock-closed')
                    ->schema([
                        Components\Grid::make(3)
                            ->schema([
                                Components\Card::make()
                                    ->schema([
                                        Components\TextEntry::make('trial_days')
                                            ->label('Trial Period')
                                            ->formatStateUsing(fn ($state) => $state ? "{$state} days" : 'No trial')
                                            ->color(fn ($state) => $state ? 'success' : 'gray')
                                            ->alignCenter(),
                                    ]),
                                    
                                Components\Card::make()
                                    ->schema([
                                        Components\TextEntry::make('duration_days')
                                            ->label('Duration')
                                            ->formatStateUsing(fn ($state) => $state ? "{$state} days" : 'Recurring')
                                            ->color(fn ($state) => $state ? 'warning' : 'gray')
                                            ->alignCenter(),
                                    ]),
                                    
                                Components\Card::make()
                                    ->schema([
                                        Components\TextEntry::make('sort_order')
                                            ->label('Sort Order')
                                            ->numeric()
                                            ->alignCenter(),
                                    ]),
                            ]),
                            
                        Components\Grid::make(2)
                            ->schema([
                                Components\Card::make()
                                    ->schema([
                                        Components\TextEntry::make('download_limit_per_month')
                                            ->label('Monthly Downloads')
                                            ->formatStateUsing(fn ($state) => $state ?? 'Unlimited')
                                            ->color(fn ($state) => $state ? 'primary' : 'success')
                                            ->weight(FontWeight::Bold)
                                            ->alignCenter(),
                                    ]),
                                    
                                Components\Card::make()
                                    ->schema([
                                        Components\TextEntry::make('download_limit_per_day')
                                            ->label('Daily Downloads')
                                            ->formatStateUsing(fn ($state) => $state ?? 'Unlimited')
                                            ->color(fn ($state) => $state ? 'primary' : 'success')
                                            ->weight(FontWeight::Bold)
                                            ->alignCenter(),
                                    ]),
                            ]),
                            
                        Components\Grid::make(3)
                            ->schema([
                                Components\IconEntry::make('has_premium_only_access')
                                    ->label('Premium Only Resources')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger')
                                    ->alignCenter(),
                                    
                                Components\IconEntry::make('allows_early_access')
                                    ->label('Early Access')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger')
                                    ->alignCenter(),
                                    
                                Components\TextEntry::make('_spacer')
                                    ->label('')
                                    ->formatStateUsing(fn () => '')
                                    ->hiddenLabel(),
                            ]),
                    ])
                    ->collapsible(),
                
                // Categories Section
                Components\Section::make('Allowed Categories')
                    ->icon('heroicon-o-folder')
                    ->schema([
                        Components\TextEntry::make('allowed_categories')
                            ->formatStateUsing(function ($state) {
                                if (empty($state)) {
                                    return new HtmlString('<p class="text-gray-400">All categories allowed</p>');
                                }
                                
                                $categories = is_array($state) ? $state : json_decode($state, true);
                                
                                if (empty($categories)) {
                                    return new HtmlString('<p class="text-gray-400">All categories allowed</p>');
                                }
                                
                                $html = '<div class="flex flex-wrap gap-2">';
                                foreach ($categories as $category) {
                                    $html .= '<span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">' . e($category) . '</span>';
                                }
                                $html .= '</div>';
                                
                                return new HtmlString($html);
                            })
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
                
                // Metadata Section
                Components\Section::make('System Information')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('created_at')
                                    ->label('Created')
                                    ->dateTime('M d, Y H:i')
                                    ->badge()
                                    ->color('gray'),
                                    
                                Components\TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->dateTime('M d, Y H:i')
                                    ->badge()
                                    ->color('gray'),
                                    
                                Components\TextEntry::make('deleted_at')
                                    ->label('Deleted')
                                    ->dateTime('M d, Y H:i')
                                    ->badge()
                                    ->color('danger')
                                    ->visible(fn ($state) => !is_null($state)),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}