<?php

namespace App\Filament\Resources\ResourceGroupResource\Pages;

use App\Filament\Resources\ResourceGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

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
}