<?php

use App\Models\Role;
use App\Models\User;
use App\Models\Module;
use App\Models\Company;
use App\Models\Permission;
use App\Models\ModuleSetting;
use App\Models\PermissionRole;
use App\Models\PermissionType;
use App\Models\UserPermission;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Schema\Blueprint;
use Modules\Accountings\Entities\Accounting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // create module and permissions
        $permissions = [
            [
                'name' => 'add_acc',
                'display_name' => 'Add Accounting'
            ],
            [
                'name' => 'view_acc',
                'display_name' => 'View Accounting'
            ],
            [
                'name' => 'edit_acc',
                'display_name' => 'Edit Accounting'
            ],
            [
                'name' => 'delete_acc',
                'display_name' => 'Delete Accounting'
            ]
        ];

        $module = new Module();
        $module->module_name = 'accountings';
        $module->description = 'User can view all Accountings.';
        $module->saveQuietly();

        $module->permissions()->createMany($permissions);

        $all = ['add_acc', 'view_acc', 'edit_acc', 'delete_acc'];
        Permission::whereIn('name', $all)->update(['allowed_permissions' => Permission::ALL_NONE]);

        $companies = Company::all();

        // We will insert these for the new company from event listener
        foreach ($companies as $company) {
            Accounting::addModuleSetting($company);
        }

        Artisan::call('module:enable accountings');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $module = Module::where('module_name', 'accountings')->first();
        $permisisons = Permission::where('module_id', $module->id)->get()->pluck('id')->toArray();
        PermissionRole::whereIn('permission_id', $permisisons)->delete();
        Module::where('module_name', 'accountings')->delete();
    }

};
