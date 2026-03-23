<?php

namespace App\Filament\Resources\ResourceGroupResource\Pages;

use App\Filament\Resources\ResourceGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\ResourceGroup;

class ListResourceGroups extends ListRecords
{
    protected static string $resource = ResourceGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Group')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            /*'all' => Tab::make('All Groups')
                ->badge(ResourceGroup::count())
                ->icon('heroicon-o-folder')
                ->modifyQueryUsing(fn (Builder $query) => $query),*/
            
            'parents' => Tab::make('Parent Groups')
                ->badge(ResourceGroup::whereNull('parent_id')->count())
                ->icon('heroicon-o-folder')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('parent_id')),
            
            'subgroups' => Tab::make('Sub Groups')
                ->badge(function () {
                    return ResourceGroup::whereNotNull('parent_id')
                        ->whereDoesntHave('parent.parent')
                        ->count();
                })
                ->icon('heroicon-o-folder-open')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->whereNotNull('parent_id')
                        ->whereDoesntHave('parent.parent');
                }),
            
            'grandgroups' => Tab::make('Grand Groups')
                ->badge(function () {
                    return ResourceGroup::whereHas('parent', function ($q) {
                        $q->whereNotNull('parent_id')
                            ->whereDoesntHave('parent.parent');
                    })->count();
                })
                ->icon('heroicon-o-folder')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->whereHas('parent', function ($q) {
                        $q->whereNotNull('parent_id')
                            ->whereDoesntHave('parent.parent');
                    });
                }),
            
            'fourth_degree' => Tab::make('4th Degree')
                ->badge(function () {
                    return ResourceGroup::whereHas('parent.parent', function ($q) {
                        $q->whereNotNull('parent_id')
                            ->whereDoesntHave('parent.parent');
                    })->count();
                })
                ->icon('heroicon-o-folder')
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->whereHas('parent.parent', function ($q) {
                        $q->whereNotNull('parent_id')
                            ->whereDoesntHave('parent.parent');
                    });
                }),
            
            'active' => Tab::make('Active')
                ->badge(ResourceGroup::where('is_active', true)->count())
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', true)),
            
            'inactive' => Tab::make('Inactive')
                ->badge(ResourceGroup::where('is_active', false)->count())
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', false)),
        ];
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'sort_order';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'asc';
    }
}