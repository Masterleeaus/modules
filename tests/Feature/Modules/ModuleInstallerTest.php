<?php

use App\Models\ModuleInstallation;
use App\Modules\HealthResult;
use App\Modules\HookResult;
use App\Modules\InstallResult;
use App\Modules\ModuleInstaller;
use App\Modules\UpgradeResult;

beforeEach(function () {
    // Ensure the module_installations table is clean before each test.
    ModuleInstallation::truncate();
});

// ---------------------------------------------------------------------------
// InstallResult
// ---------------------------------------------------------------------------

test('InstallResult::ok creates a successful result', function () {
    $result = InstallResult::ok('GroundZero', ['RunMigrations' => HookResult::ok('done')]);

    expect($result->success)->toBeTrue();
    expect($result->moduleId)->toBe('GroundZero');
    expect($result->hookResults)->toHaveKey('RunMigrations');
});

test('InstallResult::fail creates a failed result', function () {
    $result = InstallResult::fail('GroundZero', 'Something went wrong', 'RunMigrations');

    expect($result->success)->toBeFalse();
    expect($result->failedHook)->toBe('RunMigrations');
});

test('InstallResult::alreadyInstalled returns success without hook results', function () {
    $result = InstallResult::alreadyInstalled('GroundZero');

    expect($result->success)->toBeTrue();
    expect($result->message)->toContain('already installed');
});

// ---------------------------------------------------------------------------
// HookResult
// ---------------------------------------------------------------------------

test('HookResult::ok creates a passing result', function () {
    $result = HookResult::ok('Migrations ran', ['tables' => ['users']]);

    expect($result->success)->toBeTrue();
    expect($result->message)->toBe('Migrations ran');
    expect($result->details)->toBe(['tables' => ['users']]);
});

test('HookResult::fail creates a failing result', function () {
    $result = HookResult::fail('Table missing', ['missing_tables' => ['jobs']]);

    expect($result->success)->toBeFalse();
    expect($result->details['missing_tables'])->toBe(['jobs']);
});

// ---------------------------------------------------------------------------
// HealthResult
// ---------------------------------------------------------------------------

test('HealthResult::healthy creates a healthy result', function () {
    $result = HealthResult::healthy('GroundZero', ['VerifyDatabaseTables' => ['passed' => true]]);

    expect($result->healthy)->toBeTrue();
    expect($result->moduleId)->toBe('GroundZero');
});

test('HealthResult::unhealthy creates an unhealthy result', function () {
    $result = HealthResult::unhealthy('GroundZero', 'Table missing');

    expect($result->healthy)->toBeFalse();
});

// ---------------------------------------------------------------------------
// ModuleInstallation model
// ---------------------------------------------------------------------------

test('ModuleInstallation can be created with required fields', function () {
    $record = ModuleInstallation::create([
        'module_id' => 'TestModule',
        'version'   => '1.0.0',
        'status'    => 'installed',
    ]);

    expect($record->module_id)->toBe('TestModule');
    expect($record->isInstalled())->toBeTrue();
    expect($record->hasFailed())->toBeFalse();
});

test('ModuleInstallation hasFailed returns true for failed status', function () {
    $record = ModuleInstallation::create([
        'module_id' => 'BrokenModule',
        'status'    => 'failed',
    ]);

    expect($record->hasFailed())->toBeTrue();
    expect($record->isInstalled())->toBeFalse();
});

// ---------------------------------------------------------------------------
// ModuleInstaller — install
// ---------------------------------------------------------------------------

test('ModuleInstaller::install returns failure for non-existent module', function () {
    $installer = app(ModuleInstaller::class);

    $result = $installer->install('NonExistentModuleXyz');

    expect($result->success)->toBeFalse();
    expect($result->message)->toContain('not found');
});

test('ModuleInstaller::install returns alreadyInstalled when status is installed', function () {
    ModuleInstallation::create([
        'module_id' => 'GroundZero',
        'status'    => 'installed',
    ]);

    $installer = app(ModuleInstaller::class);
    $result    = $installer->install('GroundZero');

    expect($result->success)->toBeTrue();
    expect($result->message)->toContain('already installed');
});

test('ModuleInstaller::install creates a module_installations record on success', function () {
    $installer = app(ModuleInstaller::class);

    $result = $installer->install('GroundZero');

    expect($result->success)->toBeTrue();
    expect(ModuleInstallation::where('module_id', 'GroundZero')->exists())->toBeTrue();

    $record = ModuleInstallation::where('module_id', 'GroundZero')->first();
    expect($record->status)->toBe('installed');
    expect($record->installed_at)->not->toBeNull();
});

test('ModuleInstaller::install is idempotent when called twice', function () {
    $installer = app(ModuleInstaller::class);

    $installer->install('GroundZero');
    $result2 = $installer->install('GroundZero');

    expect($result2->success)->toBeTrue();
    expect(ModuleInstallation::where('module_id', 'GroundZero')->count())->toBe(1);
});

// ---------------------------------------------------------------------------
// ModuleInstaller — verify
// ---------------------------------------------------------------------------

test('ModuleInstaller::verify returns unhealthy for non-existent module', function () {
    $installer = app(ModuleInstaller::class);

    $result = $installer->verify('NonExistentModuleXyz');

    expect($result->healthy)->toBeFalse();
});

test('ModuleInstaller::verify returns a HealthResult with checks', function () {
    $installer = app(ModuleInstaller::class);

    $result = $installer->verify('GroundZero');

    expect($result)->toBeInstanceOf(HealthResult::class);
    expect($result->moduleId)->toBe('GroundZero');
    expect($result->checks)->toBeArray();
});

// ---------------------------------------------------------------------------
// ModuleInstaller — uninstall
// ---------------------------------------------------------------------------

test('ModuleInstaller::uninstall fails when no record exists', function () {
    $installer = app(ModuleInstaller::class);

    $result = $installer->uninstall('GroundZero');

    expect($result->success)->toBeFalse();
    expect($result->message)->toContain('no installation record');
});

test('ModuleInstaller::uninstall marks the record as uninstalled', function () {
    ModuleInstallation::create([
        'module_id' => 'GroundZero',
        'status'    => 'installed',
    ]);

    $installer = app(ModuleInstaller::class);
    $result    = $installer->uninstall('GroundZero');

    expect($result->success)->toBeTrue();

    $record = ModuleInstallation::where('module_id', 'GroundZero')->first();
    expect($record->status)->toBe('uninstalled');
});

// ---------------------------------------------------------------------------
// ModuleInstaller — upgrade
// ---------------------------------------------------------------------------

test('ModuleInstaller::upgrade returns noPathFound when no matching upgrade path', function () {
    $installer = app(ModuleInstaller::class);

    $result = $installer->upgrade('GroundZero', '1.0.0', '2.0.0');

    expect($result->success)->toBeFalse();
    expect($result->message)->toContain('No upgrade path');
});

// ---------------------------------------------------------------------------
// ModuleInstaller — discoverModules
// ---------------------------------------------------------------------------

test('ModuleInstaller::discoverModules returns module IDs from disk', function () {
    $installer = app(ModuleInstaller::class);
    $modules   = $installer->discoverModules();

    expect($modules)->toBeArray();
    expect($modules)->toContain('GroundZero');
});
