<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use App\Models\Category;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Main Category')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Main Categories'),
            'with_subcategories' => Tab::make('With Subcategories')
                ->modifyQueryUsing(fn (Builder $query) => $query->has('children')),
            /*'without_subcategories' => Tab::make('Without Subcategories')
                ->modifyQueryUsing(fn (Builder $query) => $query->doesntHave('children')),*/
            'with_grandcategories' => Tab::make('With Grand Categories')
                ->badge(fn () => Category::whereHas('children.children')->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('children.children')),
            'visible' => Tab::make('Visible')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_visible', true)),
            'featured' => Tab::make('Featured')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_featured', true)),
        ];
    }
}