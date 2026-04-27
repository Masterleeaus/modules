<?php

use App\Models\Company;
use App\Models\Module;
use App\Models\ModuleSetting;
use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\Role;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Purchase\Entities\PurchaseManagementSetting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $module = Module::where('module_name', 'purchase')->first();

        if (!is_null($module)) {
            $permissionName = 'view_purchase_setting';

            $permission = Permission::firstOrCreate([
                'name' => $permissionName,
                'display_name' => ucwords(str_replace('_', ' ', $permissionName)),
                'is_custom' => 1,
                'module_id' => $module->id,
                'allowed_permissions' => Permission::ALL_NONE
            ]);

            $companies = Company::all();


            foreach ($companies as $company) {

                $role = Role::where('name', 'admin')
                    ->where('company_id', $company->id)
                    ->first();

                try{
                    $permissionRole = new PermissionRole();
                    $permissionRole->permission_id = $permission->id;
                    $permissionRole->role_id = $role->id;
                    $permissionRole->permission_type_id = 4;
                    $permissionRole->save();

                }catch (\Exception $e){

                }

                $admins = User::allAdmins($company->id);

                foreach ($admins as $admin) {
                    UserPermission::firstOrCreate(
                        [
                            'user_id' => $admin->id,
                            'permission_id' => $permission->id,
                            'permission_type_id' => 4,
                        ]
                    );
                }

            }

        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
