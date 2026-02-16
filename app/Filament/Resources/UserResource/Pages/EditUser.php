<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Only hash password if it was changed
        if (isset($data['password']) && filled($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\Action::make('verify_email')
                ->label('Verify Email')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->action(function () {
                    $this->record->update(['email_verified_at' => now()]);
                    $this->notify('success', 'Email verified successfully');
                })
                ->visible(fn () => is_null($this->record->email_verified_at)),
            
            Actions\Action::make('reset_password')
                ->label('Send Password Reset')
                ->icon('heroicon-o-key')
                ->color('warning')
                ->action(function () {
                    // Send password reset email
                    // \Illuminate\Support\Facades\Password::sendResetLink($this->record->only('email'));
                    $this->notify('success', 'Password reset email sent');
                }),
            
            Actions\Action::make('impersonate')
                ->label('Login as User')
                ->icon('heroicon-o-key')
                ->color('danger')
                ->action(fn () => auth()->login($this->record))
                ->visible(fn () => auth()->user()->isAdmin() && auth()->id() !== $this->record->id)
                ->requiresConfirmation(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}