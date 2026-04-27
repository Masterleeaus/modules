<?php

namespace Modules\CleanQuality\Tests\Feature;

use Tests\TestCase;
use Modules\CleanQuality\Policies\InspectionPolicy;
use Modules\CleanQuality\Policies\QcRecordPolicy;
use Modules\CleanQuality\Support\InspectionPermissions;

/**
 * Verifies that InspectionPolicy and QcRecordPolicy correctly gate all CRUD operations
 * using both modern and legacy permission constants.
 *
 * These tests use mock User objects to avoid database interaction.
 */
class CleanQualityPolicyTest extends TestCase
{
    private function makeUser(array $permissions): \App\Models\User
    {
        $user = \Mockery::mock(\App\Models\User::class)->makePartial();

        $user->shouldReceive('hasPermissionTo')
            ->andReturnUsing(fn (string $perm) => in_array($perm, $permissions, true));

        return $user;
    }

    // ── InspectionPolicy ──────────────────────────────────────────────────────

    /** @test */
    public function inspection_policy_view_any_grants_with_view_permission(): void
    {
        $user   = $this->makeUser([InspectionPermissions::VIEW]);
        $policy = new InspectionPolicy();

        $this->assertTrue($policy->viewAny($user));
    }

    /** @test */
    public function inspection_policy_view_any_grants_with_legacy_view_permission(): void
    {
        $user   = $this->makeUser([InspectionPermissions::LEGACY_VIEW]);
        $policy = new InspectionPolicy();

        $this->assertTrue($policy->viewAny($user));
    }

    /** @test */
    public function inspection_policy_view_any_denies_without_permissions(): void
    {
        $user   = $this->makeUser([]);
        $policy = new InspectionPolicy();

        $this->assertFalse($policy->viewAny($user));
    }

    /** @test */
    public function inspection_policy_view_delegates_to_view_any(): void
    {
        $user       = $this->makeUser([InspectionPermissions::VIEW]);
        $policy     = new InspectionPolicy();
        $inspection = new \Modules\CleanQuality\Entities\Inspection();

        $this->assertSame($policy->viewAny($user), $policy->view($user, $inspection));
    }

    /** @test */
    public function inspection_policy_create_grants_with_create_permission(): void
    {
        $user   = $this->makeUser([InspectionPermissions::CREATE]);
        $policy = new InspectionPolicy();

        $this->assertTrue($policy->create($user));
    }

    /** @test */
    public function inspection_policy_create_denies_without_permissions(): void
    {
        $user   = $this->makeUser([InspectionPermissions::VIEW]);
        $policy = new InspectionPolicy();

        $this->assertFalse($policy->create($user));
    }

    /** @test */
    public function inspection_policy_update_grants_with_update_permission(): void
    {
        $user       = $this->makeUser([InspectionPermissions::UPDATE]);
        $policy     = new InspectionPolicy();
        $inspection = new \Modules\CleanQuality\Entities\Inspection();

        $this->assertTrue($policy->update($user, $inspection));
    }

    /** @test */
    public function inspection_policy_update_grants_with_legacy_update_permission(): void
    {
        $user       = $this->makeUser([InspectionPermissions::LEGACY_UPDATE]);
        $policy     = new InspectionPolicy();
        $inspection = new \Modules\CleanQuality\Entities\Inspection();

        $this->assertTrue($policy->update($user, $inspection));
    }

    /** @test */
    public function inspection_policy_delete_grants_with_delete_permission(): void
    {
        $user       = $this->makeUser([InspectionPermissions::DELETE]);
        $policy     = new InspectionPolicy();
        $inspection = new \Modules\CleanQuality\Entities\Inspection();

        $this->assertTrue($policy->delete($user, $inspection));
    }

    /** @test */
    public function inspection_policy_delete_denies_with_view_only(): void
    {
        $user       = $this->makeUser([InspectionPermissions::VIEW]);
        $policy     = new InspectionPolicy();
        $inspection = new \Modules\CleanQuality\Entities\Inspection();

        $this->assertFalse($policy->delete($user, $inspection));
    }

    // ── QcRecordPolicy ────────────────────────────────────────────────────────

    /** @test */
    public function qc_record_policy_view_any_grants_with_view_permission(): void
    {
        $user   = $this->makeUser([InspectionPermissions::VIEW]);
        $policy = new QcRecordPolicy();

        $this->assertTrue($policy->viewAny($user));
    }

    /** @test */
    public function qc_record_policy_view_any_denies_without_permissions(): void
    {
        $user   = $this->makeUser([]);
        $policy = new QcRecordPolicy();

        $this->assertFalse($policy->viewAny($user));
    }

    /** @test */
    public function qc_record_policy_view_delegates_to_view_any(): void
    {
        $user   = $this->makeUser([InspectionPermissions::VIEW]);
        $policy = new QcRecordPolicy();
        $record = new \Modules\CleanQuality\Entities\QcRecord();

        $this->assertSame($policy->viewAny($user), $policy->view($user, $record));
    }

    /** @test */
    public function qc_record_policy_update_grants_with_update_permission(): void
    {
        $user   = $this->makeUser([InspectionPermissions::UPDATE]);
        $policy = new QcRecordPolicy();
        $record = new \Modules\CleanQuality\Entities\QcRecord();

        $this->assertTrue($policy->update($user, $record));
    }

    /** @test */
    public function qc_record_policy_delete_denies_with_view_only(): void
    {
        $user   = $this->makeUser([InspectionPermissions::VIEW]);
        $policy = new QcRecordPolicy();
        $record = new \Modules\CleanQuality\Entities\QcRecord();

        $this->assertFalse($policy->delete($user, $record));
    }
}
