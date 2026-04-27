<?php

use App\Models\ModuleInstallation;

beforeEach(function () {
    ModuleInstallation::truncate();
});

// ---------------------------------------------------------------------------
// titan:module:install
// ---------------------------------------------------------------------------

test('titan:module:install succeeds for an existing module', function () {
    $this->artisan('titan:module:install', ['module' => 'GroundZero'])
        ->assertSuccessful();

    expect(ModuleInstallation::where('module_id', 'GroundZero')->where('status', 'installed')->exists())->toBeTrue();
});

test('titan:module:install exits with failure for a non-existent module', function () {
    $this->artisan('titan:module:install', ['module' => 'NoSuchModuleXyz123'])
        ->assertFailed();
});

test('titan:module:install is idempotent — running twice succeeds', function () {
    $this->artisan('titan:module:install', ['module' => 'GroundZero'])->assertSuccessful();
    $this->artisan('titan:module:install', ['module' => 'GroundZero'])->assertSuccessful();

    expect(ModuleInstallation::where('module_id', 'GroundZero')->count())->toBe(1);
});

test('titan:module:install --force re-installs an already-installed module', function () {
    // First install
    $this->artisan('titan:module:install', ['module' => 'GroundZero'])->assertSuccessful();

    // Force re-install
    $this->artisan('titan:module:install', ['module' => 'GroundZero', '--force' => true])
        ->assertSuccessful();

    expect(ModuleInstallation::where('module_id', 'GroundZero')->count())->toBe(1);
});

// ---------------------------------------------------------------------------
// titan:module:status
// ---------------------------------------------------------------------------

test('titan:module:status succeeds and outputs a table', function () {
    $this->artisan('titan:module:status')
        ->assertSuccessful()
        ->expectsOutputToContain('Module');
});

test('titan:module:status lists installed modules', function () {
    ModuleInstallation::create([
        'module_id' => 'GroundZero',
        'status'    => 'installed',
        'version'   => '1.0.0',
    ]);

    $this->artisan('titan:module:status')
        ->assertSuccessful()
        ->expectsOutputToContain('GroundZero');
});

// ---------------------------------------------------------------------------
// titan:module:verify
// ---------------------------------------------------------------------------

test('titan:module:verify succeeds for an existing module', function () {
    $this->artisan('titan:module:verify', ['module' => 'GroundZero'])
        ->assertSuccessful();
});

test('titan:module:verify exits with failure for a non-existent module', function () {
    $this->artisan('titan:module:verify', ['module' => 'NoSuchModuleXyz123'])
        ->assertFailed();
});

test('titan:module:verify --all succeeds when all modules are healthy', function () {
    $this->artisan('titan:module:verify', ['--all' => true])
        ->assertSuccessful();
});

test('titan:module:verify requires a module argument or --all flag', function () {
    $this->artisan('titan:module:verify')
        ->assertFailed();
});

// ---------------------------------------------------------------------------
// titan:module:upgrade
// ---------------------------------------------------------------------------

test('titan:module:upgrade exits with failure when no upgrade path exists', function () {
    ModuleInstallation::create([
        'module_id' => 'GroundZero',
        'status'    => 'installed',
        'version'   => '1.0.0',
    ]);

    $this->artisan('titan:module:upgrade', [
        'module' => 'GroundZero',
        '--from' => '1.0.0',
        '--to'   => '99.0.0',
    ])->assertFailed();
});

test('titan:module:upgrade exits with failure for a non-existent module', function () {
    $this->artisan('titan:module:upgrade', [
        'module' => 'NoSuchModuleXyz123',
        '--from' => '1.0.0',
        '--to'   => '2.0.0',
    ])->assertFailed();
});
