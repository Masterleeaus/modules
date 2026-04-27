<?php

namespace Modules\TitanSolo\Services;

use App\Models\OrganizationSetting;

class SoloModeService
{
    /**
     * Detect whether an organisation is in solo mode.
     */
    public function isSolo(int $organizationId): bool
    {
        $settings = OrganizationSetting::where('organization_id', $organizationId)->first();

        return $settings && $settings->mode === 'solo';
    }

    /**
     * Switch an organisation to solo mode.
     */
    public function enableSolo(int $organizationId): void
    {
        OrganizationSetting::where('organization_id', $organizationId)
            ->update(['mode' => 'solo']);
    }

    /**
     * Switch an organisation to team mode.
     */
    public function enableTeam(int $organizationId): void
    {
        OrganizationSetting::where('organization_id', $organizationId)
            ->update(['mode' => 'team']);
    }
}
