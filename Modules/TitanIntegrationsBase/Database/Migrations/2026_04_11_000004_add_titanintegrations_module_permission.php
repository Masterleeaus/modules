<?php

use App\Models\Module;
use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $module = Module::firstOrCreate(['module_name' => 'titanintegrations']);

        $permissions = [
            ['name' => 'view_integrations',     'display_name' => 'View Integrations',     'module_id' => $module->id, 'allowed_permissions' => Permission::ALL_NONE],
            ['name' => 'manage_integrations',   'display_name' => 'Manage Integrations',   'module_id' => $module->id, 'allowed_permissions' => Permission::ALL_NONE],
            ['name' => 'manage_api_tokens',     'display_name' => 'Manage API Tokens',     'module_id' => $module->id, 'allowed_permissions' => Permission::ALL_NONE],
            ['name' => 'manage_webhooks',       'display_name' => 'Manage Webhooks',       'module_id' => $module->id, 'allowed_permissions' => Permission::ALL_NONE],
            ['name' => 'view_integration_logs', 'display_name' => 'View Integration Logs', 'module_id' => $module->id, 'allowed_permissions' => Permission::ALL_NONE],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate($permission);
        }
    }

    public function down(): void
    {
        $permissionNames = [
            'view_integrations', 'manage_integrations',
            'manage_api_tokens', 'manage_webhooks', 'view_integration_logs',
        ];
        Permission::whereIn('name', $permissionNames)->delete();
        Module::where('module_name', 'titanintegrations')->delete();
    }
};
