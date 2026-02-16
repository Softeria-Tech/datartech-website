<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
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
                ->label('New Category')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Categories'),
            'root' => Tab::make('Main Categories')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('parent_id')),
            'sub' => Tab::make('Sub-categories')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('parent_id')),
            'visible' => Tab::make('Visible')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_visible', true)),
            'featured' => Tab::make('Featured')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_featured', true)),
        ];
    }
}