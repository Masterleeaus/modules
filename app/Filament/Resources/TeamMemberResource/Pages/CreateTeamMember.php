<?php

namespace App\Filament\Resources\TeamMemberResource\Pages;

use App\Filament\Resources\TeamMemberResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTeamMember extends CreateRecord
{
    protected static string $resource = TeamMemberResource::class;

    protected function afterCreate(): void
    {
        $role = $this->form->getState()['roles'] ?? null;
        if ($role) {
            $this->record->assignRole($role);
        }
    }
}
