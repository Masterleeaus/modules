<?php

use App\Models\Company;
use App\Models\Module;
use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\Role;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Database\Migrations\Migration;
use Modules\Biometric\Entities\BiometricGlobalSetting;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds view_biometric, manage_biometric_devices, export_attendance permissions.
     */
    public function up(): void
    {
        $moduleName = BiometricGlobalSetting::MODULE_NAME;
        $module = Module::firstOrCreate(['module_name' => $moduleName]);

        $permissions = [
            [
                'name' => 'view_biometric',
                'allowed_permissions' => Permission::ALL_NONE,
                'is_custom' => 1,
            ],
            [
                'name' => 'manage_biometric_devices',
                'allowed_permissions' => Permission::ALL_NONE,
                'is_custom' => 1,
            ],
            [
                'name' => 'export_attendance',
                'allowed_permissions' => Permission::ALL_NONE,
                'is_custom' => 1,
            ],
        ];

        foreach ($permissions as $permissionData) {
            $permission = Permission::updateOrCreate(
                [
                    'name'      => $permissionData['name'],
                    'module_id' => $module->id,
                ],
                [
                    'display_name'         => ucwords(str_replace('_', ' ', $permissionData['name'])),
                    'is_custom'            => $permissionData['is_custom'],
                    'allowed_permissions'  => $permissionData['allowed_permissions'],
                ]
            );

            $companies = Company::all();

            foreach ($companies as $company) {
                $adminRole = Role::where('name', 'admin')
                    ->where('company_id', $company->id)
                    ->first();

                if ($adminRole) {
                    $pr = PermissionRole::where('permission_id', $permission->id)
                        ->where('role_id', $adminRole->id)
                        ->first() ?: new PermissionRole();

                    $pr->permission_id      = $permission->id;
                    $pr->role_id            = $adminRole->id;
                    $pr->permission_type_id = 4; // All
                    $pr->save();
                }
            }

            $adminUsers = User::allAdmins();

            foreach ($adminUsers as $adminUser) {
                $up = UserPermission::where('user_id', $adminUser->id)
                    ->where('permission_id', $permission->id)
                    ->first() ?: new UserPermission();

                $up->user_id            = $adminUser->id;
                $up->permission_id      = $permission->id;
                $up->permission_type_id = 4; // All
                $up->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Permissions are intentionally left in place on rollback to avoid
        // breaking existing role/user assignments.
    }
};
