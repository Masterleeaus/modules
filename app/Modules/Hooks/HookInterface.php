<?php

namespace App\Modules\Hooks;

use App\Modules\HookResult;

interface HookInterface
{
    /**
     * Execute the hook for the given module.
     *
     * @param  string  $moduleId  The module identifier (matches folder name under Modules/).
     * @param  array<string, mixed>  $context  Shared context/state across hooks in a run.
     */
    public function handle(string $moduleId, array &$context): HookResult;
}
