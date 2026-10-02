<?php

namespace Modules\HRCore\Filament\Resources\TeamMemberResource\Pages;

use Modules\HRCore\Filament\Resources\TeamMemberResource;
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
