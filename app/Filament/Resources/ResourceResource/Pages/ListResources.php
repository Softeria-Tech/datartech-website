<?php

namespace App\Filament\Resources\ResourceResource\Pages;

use App\Filament\Resources\ResourceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListResources extends ListRecords
{
    protected static string $resource = ResourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Resource')
                ->icon('heroicon-o-plus'),
            Actions\Action::make('import')
                ->label('Import Resources')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('gray'),
        ];
    }
}