<?php

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds granular complaint-management permissions.
     */
    public function up(): void
    {
        $newPermissions = [
            [
                'name'         => 'view_complaints',
                'display_name' => 'View Complaints',
            ],
            [
                'name'         => 'manage_complaints',
                'display_name' => 'Manage Complaints',
            ],
            [
                'name'         => 'resolve_complaints',
                'display_name' => 'Resolve Complaints',
            ],
            [
                'name'         => 'approve_refund',
                'display_name' => 'Approve Refund',
            ],
        ];

        $module = \App\Models\Module::where('module_name', 'complaint')->first();

        if (!$module) {
            return;
        }

        foreach ($newPermissions as $perm) {
            if (!Permission::where('name', $perm['name'])->exists()) {
                $module->permissions()->create(array_merge($perm, [
                    'allowed_permissions' => Permission::ALL_4_OWNED_2_NONE_5,
                ]));
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Permission::whereIn('name', [
            'view_complaints',
            'manage_complaints',
            'resolve_complaints',
            'approve_refund',
        ])->delete();
    }
};
