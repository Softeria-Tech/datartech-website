<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
            Actions\Action::make('impersonate')
                ->label('Login as User')
                ->icon('heroicon-o-key')
                ->color('warning')
                ->action(fn () => auth()->login($this->record))
                ->visible(fn () => auth()->user()->isAdmin() && auth()->id() !== $this->record->id)
                ->requiresConfirmation(),
            Actions\Action::make('back')
                ->label('Back to Users')
                ->icon('heroicon-o-arrow-left')
                ->url(static::getResource()::getUrl('index'))
                ->color('gray'),
        ];
    }
}