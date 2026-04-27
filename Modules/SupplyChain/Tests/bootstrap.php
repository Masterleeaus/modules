<?php

/**
 * Bootstrap for SupplyChain unit tests.
 * Loads only the SupplyChain module PSR-4 namespace — no Laravel app required.
 */

// Map Modules\SupplyChain namespace to the module src directory.
spl_autoload_register(function (string $class): void {
    $prefix = 'Modules\\SupplyChain\\';
    $base   = __DIR__ . '/../';

    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file     = $base . str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// Minimal stubs for classes that the module classes reference but don't need to
// be fully functional in unit tests.

if (!class_exists(\Illuminate\Foundation\Support\Providers\EventServiceProvider::class)) {
    class_alias(
        \Modules\SupplyChain\Tests\Stubs\MinimalServiceProvider::class,
        \Illuminate\Foundation\Support\Providers\EventServiceProvider::class
    );
}
