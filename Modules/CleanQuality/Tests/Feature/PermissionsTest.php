<?php

namespace Modules\CleanQuality\Tests\Feature;

use Tests\TestCase;
use Modules\CleanQuality\Support\InspectionPermissions;

class PermissionsTest extends TestCase
{
    /** @test */
    public function permissions_keys_are_defined(): void
    {
        $this->assertSame('view_quality_control',   InspectionPermissions::VIEW);
        $this->assertSame('add_quality_control',    InspectionPermissions::CREATE);
        $this->assertSame('edit_quality_control',   InspectionPermissions::UPDATE);
        $this->assertSame('delete_quality_control', InspectionPermissions::DELETE);
    }

    /** @test */
    public function legacy_permission_keys_are_defined(): void
    {
        $this->assertSame('inspection.view',   InspectionPermissions::LEGACY_VIEW);
        $this->assertSame('inspection.create', InspectionPermissions::LEGACY_CREATE);
        $this->assertSame('inspection.update', InspectionPermissions::LEGACY_UPDATE);
        $this->assertSame('inspection.delete', InspectionPermissions::LEGACY_DELETE);
    }

    /** @test */
    public function permission_map_covers_all_crud_keys(): void
    {
        $map = InspectionPermissions::MAP;

        $this->assertArrayHasKey(InspectionPermissions::VIEW,   $map);
        $this->assertArrayHasKey(InspectionPermissions::CREATE, $map);
        $this->assertArrayHasKey(InspectionPermissions::UPDATE, $map);
        $this->assertArrayHasKey(InspectionPermissions::DELETE, $map);
    }

    /** @test */
    public function legacy_for_returns_correct_mapping(): void
    {
        $this->assertSame(
            InspectionPermissions::LEGACY_VIEW,
            InspectionPermissions::legacyFor(InspectionPermissions::VIEW)
        );

        $this->assertNull(InspectionPermissions::legacyFor('nonexistent_permission'));
    }
}

