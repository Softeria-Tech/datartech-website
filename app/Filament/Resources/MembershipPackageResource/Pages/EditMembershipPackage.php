<?php

namespace App\Filament\Resources\MembershipPackageResource\Pages;

use App\Filament\Resources\MembershipPackageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMembershipPackage extends EditRecord
{
    protected static string $resource = MembershipPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
