<?php

namespace App\Modules;

use App\Models\ModuleInstallation;
use App\Modules\Hooks\HookInterface;
use App\Modules\Hooks\RegisterPermissions;
use App\Modules\Hooks\RegisterSignals;
use App\Modules\Hooks\RunMigrations;
use App\Modules\Hooks\SeedDefaultData;
use App\Modules\Hooks\SeedDefaultTemplates;
use App\Modules\Hooks\VerifyConfigKeys;
use App\Modules\Hooks\VerifyDatabaseTables;
use App\Modules\Hooks\VerifyPermissionsRegistered;

class ModuleInstaller
{
    /**
     * Built-in hook name → class map.
     *
     * @var array<string, class-string<HookInterface>>
     */
    private array $hookRegistry = [
        'RunMigrations'             => RunMigrations::class,
        'SeedDefaultData'           => SeedDefaultData::class,
        'RegisterPermissions'       => RegisterPermissions::class,
        'RegisterSignals'           => RegisterSignals::class,
        'SeedDefaultTemplates'      => SeedDefaultTemplates::class,
        'VerifyDatabaseTables'      => VerifyDatabaseTables::class,
        'VerifyConfigKeys'          => VerifyConfigKeys::class,
        'VerifyPermissionsRegistered' => VerifyPermissionsRegistered::class,
    ];

    /**
     * Install a module.
     *
     * Install is idempotent: if the module is already marked 'installed' the
     * method returns early without re-running hooks.
     */
    public function install(string $moduleId): InstallResult
    {
        if (! $this->moduleExists($moduleId)) {
            return InstallResult::fail($moduleId, "Module directory not found: Modules/{$moduleId}");
        }

        $record = ModuleInstallation::where('module_id', $moduleId)->first();

        if ($record && $record->status === 'installed') {
            return InstallResult::alreadyInstalled($moduleId);
        }

        $version = $this->resolveVersion($moduleId);
        $lifecycle = $this->loadLifecycle($moduleId);

        // Upsert record to 'installing'
        $record = ModuleInstallation::updateOrCreate(
            ['module_id' => $moduleId],
            [
                'version'    => $version,
                'status'     => 'installing',
                // auth()->id() returns null in console context — the column is nullable.
                'installed_by' => auth()->id() ?? null,
            ],
        );

        $hookNames = $lifecycle['install'] ?? ['RunMigrations', 'SeedDefaultData', 'RegisterPermissions'];
        $context = [];
        $hookResults = [];

        foreach ($hookNames as $hookName) {
            $result = $this->runHook($hookName, $moduleId, $context);
            $hookResults[$hookName] = $result;

            if (! $result->success) {
                $record->update(['status' => 'failed']);

                return InstallResult::fail(
                    $moduleId,
                    "Hook [{$hookName}] failed: {$result->message}",
                    $hookName,
                    $hookResults,
                );
            }
        }

        // Run post-install verification hooks (failures are reported but do
        // NOT roll back — they are advisory health checks).
        $postHooks = $lifecycle['post_install'] ?? ['VerifyDatabaseTables', 'VerifyPermissionsRegistered'];
        foreach ($postHooks as $hookName) {
            $hookResults[$hookName] = $this->runHook($hookName, $moduleId, $context);
        }

        $record->update([
            'status'       => 'installed',
            'installed_at' => now(),
            'version'      => $version,
        ]);

        return InstallResult::ok($moduleId, $hookResults);
    }

    /**
     * Run upgrade hooks for the given version transition.
     */
    public function upgrade(string $moduleId, string $fromVersion, string $toVersion): UpgradeResult
    {
        if (! $this->moduleExists($moduleId)) {
            return UpgradeResult::fail($moduleId, $fromVersion, $toVersion, "Module directory not found: Modules/{$moduleId}");
        }

        $lifecycle = $this->loadLifecycle($moduleId);
        $upgradePaths = $lifecycle['upgrade'] ?? [];

        // lifecycle.json may use either the Unicode arrow (→) or the ASCII
        // arrow (->) as the separator between versions. Both are supported so
        // that module authors can choose the style that reads best in JSON.
        $key    = "{$fromVersion}→{$toVersion}";
        $altKey = "{$fromVersion}->{$toVersion}";

        $hooks = $upgradePaths[$key] ?? $upgradePaths[$altKey] ?? null;

        if ($hooks === null) {
            return UpgradeResult::noPathFound($moduleId, $fromVersion, $toVersion);
        }

        $record = ModuleInstallation::where('module_id', $moduleId)->first();
        if ($record) {
            $record->update(['status' => 'upgrading']);
        }

        $context = [];
        $hookResults = [];

        foreach ($hooks as $hookName) {
            $result = $this->runHook($hookName, $moduleId, $context);
            $hookResults[$hookName] = $result;

            if (! $result->success) {
                if ($record) {
                    $record->update(['status' => 'failed']);
                }

                return UpgradeResult::fail(
                    $moduleId,
                    $fromVersion,
                    $toVersion,
                    "Hook [{$hookName}] failed: {$result->message}",
                    $hookResults,
                );
            }
        }

        if ($record) {
            $record->update([
                'status'           => 'installed',
                'version'          => $toVersion,
                'last_upgraded_at' => now(),
            ]);
        }

        return UpgradeResult::ok($moduleId, $fromVersion, $toVersion, $hookResults);
    }

    /**
     * Mark a module as uninstalled (data is preserved by default).
     */
    public function uninstall(string $moduleId): UninstallResult
    {
        $record = ModuleInstallation::where('module_id', $moduleId)->first();

        if (! $record) {
            return UninstallResult::fail($moduleId, "Module {$moduleId} has no installation record.");
        }

        $record->update(['status' => 'uninstalled']);

        return UninstallResult::ok($moduleId);
    }

    /**
     * Run post-install health checks.
     */
    public function verify(string $moduleId): HealthResult
    {
        if (! $this->moduleExists($moduleId)) {
            return HealthResult::unhealthy($moduleId, "Module directory not found: Modules/{$moduleId}");
        }

        $lifecycle = $this->loadLifecycle($moduleId);
        $hookNames = $lifecycle['post_install'] ?? ['VerifyDatabaseTables', 'VerifyPermissionsRegistered'];

        $context = [];
        $checks = [];
        $allPassed = true;

        foreach ($hookNames as $hookName) {
            $result = $this->runHook($hookName, $moduleId, $context);
            $checks[$hookName] = [
                'passed'  => $result->success,
                'message' => $result->message,
                'details' => $result->details,
            ];
            if (! $result->success) {
                $allPassed = false;
            }
        }

        if ($allPassed) {
            return HealthResult::healthy($moduleId, $checks);
        }

        $failed = array_keys(array_filter($checks, fn ($c) => ! $c['passed']));

        return HealthResult::unhealthy(
            $moduleId,
            "Module {$moduleId} failed health checks: " . implode(', ', $failed),
            $checks,
        );
    }

    /**
     * Roll back a module to a previously installed version.
     *
     * Currently marks the record and re-runs the install hooks for the target
     * version. A richer implementation would reverse migrations — that is
     * left for per-module lifecycle.json `rollback` hooks.
     */
    public function rollback(string $moduleId, string $toVersion): RollbackResult
    {
        if (! $this->moduleExists($moduleId)) {
            return RollbackResult::fail($moduleId, $toVersion, "Module directory not found: Modules/{$moduleId}");
        }

        $record = ModuleInstallation::where('module_id', $moduleId)->first();

        if (! $record) {
            return RollbackResult::fail($moduleId, $toVersion, "No installation record found for {$moduleId}.");
        }

        $lifecycle = $this->loadLifecycle($moduleId);
        $rollbackHooks = $lifecycle['rollback'] ?? [];

        $context = [];
        foreach ($rollbackHooks as $hookName) {
            $result = $this->runHook($hookName, $moduleId, $context);
            if (! $result->success) {
                return RollbackResult::fail($moduleId, $toVersion, "Rollback hook [{$hookName}] failed: {$result->message}");
            }
        }

        $record->update([
            'version' => $toVersion,
            'status'  => 'installed',
        ]);

        return RollbackResult::ok($moduleId, $toVersion);
    }

    /**
     * Return all module IDs that have an entry in module_installations.
     *
     * @return ModuleInstallation[]
     */
    public function allInstallations(): array
    {
        return ModuleInstallation::orderBy('module_id')->get()->all();
    }

    /**
     * Discover all modules that have a module.json on disk.
     *
     * @return string[]
     */
    public function discoverModules(): array
    {
        $modulesPath = base_path('Modules');

        if (! is_dir($modulesPath)) {
            return [];
        }

        $modules = [];
        foreach (glob("{$modulesPath}/*/module.json") ?: [] as $manifestPath) {
            $modules[] = basename(dirname($manifestPath));
        }

        return $modules;
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function moduleExists(string $moduleId): bool
    {
        return is_dir(base_path("Modules/{$moduleId}"));
    }

    private function resolveVersion(string $moduleId): string
    {
        $manifestPath = base_path("Modules/{$moduleId}/module.json");

        if (file_exists($manifestPath)) {
            $manifest = json_decode(file_get_contents($manifestPath), true);
            $version = $manifest['version'] ?? null;
            if ($version !== null) {
                return (string) $version;
            }
        }

        return '1.0.0';
    }

    /**
     * Load the lifecycle.json for the module.
     *
     * Supports both the standardised `lifecycle.json` name and the legacy
     * `lifecycle_manifest.json` found in some existing modules.
     *
     * @return array<string, mixed>
     */
    private function loadLifecycle(string $moduleId): array
    {
        $candidates = [
            base_path("Modules/{$moduleId}/manifests/lifecycle.json"),
            base_path("Modules/{$moduleId}/manifests/lifecycle_manifest.json"),
        ];

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                $data = json_decode(file_get_contents($path), true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
                    return $data;
                }
            }
        }

        // Return sensible defaults when no lifecycle manifest is present.
        return [
            'install'      => ['RunMigrations', 'SeedDefaultData', 'RegisterPermissions', 'RegisterSignals', 'SeedDefaultTemplates'],
            'post_install' => ['VerifyDatabaseTables', 'VerifyPermissionsRegistered'],
            'upgrade'      => [],
            'rollback'     => [],
        ];
    }

    private function runHook(string $hookName, string $moduleId, array &$context): HookResult
    {
        // Allow fully-qualified class names in lifecycle.json.
        if (class_exists($hookName)) {
            /** @var HookInterface $hook */
            $hook = app($hookName);

            return $hook->handle($moduleId, $context);
        }

        if (isset($this->hookRegistry[$hookName])) {
            /** @var HookInterface $hook */
            $hook = app($this->hookRegistry[$hookName]);

            return $hook->handle($moduleId, $context);
        }

        return HookResult::fail("Unknown hook: {$hookName}");
    }
}
