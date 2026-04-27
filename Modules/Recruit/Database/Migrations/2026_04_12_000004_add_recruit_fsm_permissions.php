<?php

use App\Models\Module;
use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Seed recruit-specific permissions for the new applicant tracking
     * and compliance features.
     */
    public function up(): void
    {
        $module = Module::firstOrCreate(['module_name' => 'recruit']);

        $permissions = [
            [
                'name'          => 'view_applicants',
                'display_name'  => 'View Applicants',
                'allowed_permissions' => Permission::ALL_NONE,
            ],
            [
                'name'          => 'manage_recruitment',
                'display_name'  => 'Manage Recruitment',
                'allowed_permissions' => Permission::ALL_NONE,
            ],
            [
                'name'          => 'hire_applicant',
                'display_name'  => 'Hire Applicant',
                'allowed_permissions' => Permission::ALL_NONE,
            ],
            [
                'name'          => 'view_compliance',
                'display_name'  => 'View Compliance',
                'allowed_permissions' => Permission::ALL_NONE,
            ],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm['name'], 'module_id' => $module->id],
                [
                    'display_name'        => $perm['display_name'],
                    'is_custom'           => 1,
                    'module_id'           => $module->id,
                    'allowed_permissions' => $perm['allowed_permissions'],
                ]
            );
        }
    }

    public function down(): void
    {
        $module = Module::where('module_name', 'recruit')->first();

        if ($module) {
            Permission::where('module_id', $module->id)
                ->whereIn('name', ['view_applicants', 'manage_recruitment', 'hire_applicant', 'view_compliance'])
                ->delete();
        }
    }
};
