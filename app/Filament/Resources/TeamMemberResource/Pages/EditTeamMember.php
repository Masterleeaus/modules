<?php

namespace App\Filament\Resources\TeamMemberResource\Pages;

use App\Filament\Resources\TeamMemberResource;
use Filament\Resources\Pages\EditRecord;

class EditTeamMember extends EditRecord
{
    protected static string $resource = TeamMemberResource::class;

    protected function afterSave(): void
    {
        $role = $this->form->getState()['roles'] ?? null;
        if ($role) {
            $this->record->syncRoles([$role]);
        }
    }
}
