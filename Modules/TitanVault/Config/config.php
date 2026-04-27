<?php

return [
    'name' => 'TitanVault',

    /*
    |--------------------------------------------------------------------------
    | Storage
    |--------------------------------------------------------------------------
    */

    // Storage disk to use ('local', 's3', etc.).
    'storage_disk' => env('TITAN_VAULT_DISK', 'local'),

    // Base path within the storage disk.
    'storage_path' => 'titan-vault',

    /*
    |--------------------------------------------------------------------------
    | Access Links
    |--------------------------------------------------------------------------
    */

    // Default number of days before a proof link expires. 0 = no expiry.
    'default_expiry_days' => (int) env('TITAN_VAULT_EXPIRY_DAYS', 30),

    // Whether newly-generated access links require a password by default.
    'require_password' => (bool) env('TITAN_VAULT_REQUIRE_PASSWORD', false),
];
