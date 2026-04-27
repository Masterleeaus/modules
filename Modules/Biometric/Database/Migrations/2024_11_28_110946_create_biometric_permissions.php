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
     */
    public function up(): void
    {
        $moduleName = BiometricGlobalSetting::MODULE_NAME;
        $module = Module::firstOrCreate(['module_name' => $moduleName]);

        $permissions = [
            [
                'module_id' => $module->id,
                'name' => 'manage_biometric_settings',
                'allowed_permissions' => Permission::ALL_NONE,
                'is_custom' => 1
            ],
        ];

        foreach ($permissions as $permissionData) {
            $permission = Permission::updateOrCreate(
                [
                    'name' => $permissionData['name'],
                    'module_id' => $permissionData['module_id'],
                ],
                [
                    'display_name' => ucwords(str_replace('_', ' ', $permissionData['name'])),
                    'is_custom' => $permissionData['is_custom'],
                    'allowed_permissions' => $permissionData['allowed_permissions'],
                ]
            );
            $companies = Company::all();

            foreach ($companies as $company) {
                $role = Role::where('name', 'admin')
                    ->where('company_id', $company->id)
                    ->first();

                if ($role) {
                    $permissionRole = PermissionRole::where('permission_id', $permission->id)
                        ->where('role_id', $role->id)
                        ->first();

                    $permissionRole = $permissionRole ?: new PermissionRole();
                    $permissionRole->permission_id = $permission->id;
                    $permissionRole->role_id = $role->id;
                    $permissionRole->permission_type_id = 4; // All
                    $permissionRole->save();
                }

                BiometricGlobalSetting::addModuleSetting($company);
            }

            $adminUsers = User::allAdmins();

            foreach ($adminUsers as $adminUser) {
                $userPermission = UserPermission::where('user_id', $adminUser->id)->where('permission_id', $permission->id)->first() ?: new UserPermission();
                $userPermission->user_id = $adminUser->id;
                $userPermission->permission_id = $permission->id;
                $userPermission->permission_type_id = 4; // All
                $userPermission->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
