<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Tabs;

class ViewCategory extends ViewRecord
{
    protected static string $resource = CategoryResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Category Overview')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Category')
                                    ->weight('bold')
                                    ->size(TextEntry\TextEntrySize::Large),
                                
                                TextEntry::make('parent.name')
                                    ->label('Parent Category')
                                    ->placeholder('Main Category')
                                    ->badge()
                                    ->color('info'),
                                
                                TextEntry::make('depth')
                                    ->label('Level')
                                    ->badge()
                                    ->formatStateUsing(fn ($state) => $state === 0 ? 'Main Category' : "Subcategory Level {$state}"),
                            ]),
                    ]),
                
                Tabs::make('Category Details')
                    ->tabs([
                        Tabs\Tab::make('Overview')
                            ->schema([
                                Section::make('Description')
                                    ->schema([
                                        TextEntry::make('short_description')
                                            ->label('Short Description')
                                            ->placeholder('No short description'),
                                        TextEntry::make('description')
                                            ->label('Full Description')
                                            ->html()
                                            ->placeholder('No description'),
                                    ]),
                                
                                Section::make('Statistics')
                                    ->schema([
                                        Grid::make(3)
                                            ->schema([
                                                TextEntry::make('resources_count')
                                                    ->label('Resources')
                                                    ->numeric()
                                                    ->badge()
                                                    ->color('success'),
                                                TextEntry::make('children_count')
                                                    ->label('Sub-categories')
                                                    ->getStateUsing(fn ($record) => $record->children()->count())
                                                    ->badge()
                                                    ->color('info'),
                                            ]),
                                    ]),
                            ]),
                        
                        Tabs\Tab::make('Subcategories')
                            ->visible(fn ($record) => $record->children()->count() > 0)
                            ->schema([
                                RepeatableEntry::make('children')
                                    ->label('')
                                    ->schema([
                                        Grid::make(4)
                                            ->schema([
                                                TextEntry::make('name')
                                                    ->label('Name')
                                                    ->weight('bold'),
                                                TextEntry::make('resources_count')
                                                    ->label('Resources')
                                                    ->numeric()
                                                    ->badge()
                                                    ->color('success'),
                                                IconEntry::make('is_visible')
                                                    ->label('Visible')
                                                    ->boolean(),
                                                TextEntry::make('sort_order')
                                                    ->label('Order')
                                                    ->numeric(),
                                            ]),
                                    ])
                                    ->grid(2),
                            ]),
                        
                        Tabs\Tab::make('Images')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        ImageEntry::make('thumbnail')
                                            ->label('Thumbnail')
                                            ->width(150)
                                            ->height(150)
                                            ->defaultImageUrl(url('/assets/frontend/images/default-category.png')),
                                        ImageEntry::make('cover_image')
                                            ->label('Cover Image')
                                            ->width(300)
                                            ->height(100)
                                            ->defaultImageUrl(url('/assets/frontend/images/default-category.png')),
                                    ]),
                            ])
                            ->visible(fn ($record) => $record->thumbnail || $record->cover_image),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('manage_subcategories')
                ->label('Manage Subcategories')
                ->icon('heroicon-o-folder')
                ->color('info')
                ->url(fn ($record) => CategoryResource::getUrl('subcategories', ['record' => $record]))
                ->visible(fn ($record) => $record->isRoot()),
            
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
            Actions\Action::make('back')
                ->label('Back to Categories')
                ->icon('heroicon-o-arrow-left')
                ->url(CategoryResource::getUrl('index'))
                ->color('gray'),
        ];
    }
}