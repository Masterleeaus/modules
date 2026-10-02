<?php

namespace App\Signals;

/**
 * Discovers and caches signal contracts from every module's
 * manifests/signals.json and from the core app manifest.
 *
 * Contract format (emits[] entry):
 * {
 *   "type": "job.created",
 *   "required_fields": ["job_id"],
 *   "approval_mode": "auto",
 *   "description": "..."
 * }
 */
class SignalRegistry
{
    /** @var array<string, array<string, mixed>>|null */
    private ?array $contracts = null;

    /**
     * Return the contract for a given signal type, or null if not registered.
     *
     * @return array<string, mixed>|null
     */
    public function getContract(string $type): ?array
    {
        return $this->all()[$type] ?? null;
    }

    /**
     * Return all registered signal contracts keyed by type.
     *
     * @return array<string, array<string, mixed>>
     */
    public function all(): array
    {
        if ($this->contracts !== null) {
            return $this->contracts;
        }

        return $this->contracts = $this->discover();
    }

    /**
     * Force a refresh of the cached contracts. Useful in tests.
     */
    public function flush(): void
    {
        $this->contracts = null;
    }

    /** @return array<string, array<string, mixed>> */
    private function discover(): array
    {
        $contracts = [];

        // Core app signal contracts
        $coreManifest = __DIR__ . '/manifests/signals.json';
        if (file_exists($coreManifest)) {
            $this->loadManifest($contracts, $coreManifest);
        }

        // Auto-discover module manifests: Modules/*/manifests/signals.json
        $modulesPath = base_path('Modules');
        if (is_dir($modulesPath)) {
            foreach (glob("{$modulesPath}/*/manifests/signals.json") ?: [] as $manifestPath) {
                $this->loadManifest($contracts, $manifestPath);
            }
        }

        return $contracts;
    }

    /** @param array<string, array<string, mixed>> $contracts */
    private function loadManifest(array &$contracts, string $path): void
    {
        $json = file_get_contents($path);
        if ($json === false) {
            return;
        }

        $data = json_decode($json, true);
        if (! is_array($data)) {
            return;
        }

        foreach ($data['emits'] ?? [] as $signal) {
            if (isset($signal['type'])) {
                $contracts[$signal['type']] = $signal;
            }
        }
    }
}
