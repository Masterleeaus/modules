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
use Modules\Units\Entities\Unit;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\SupplyChain\Entities\Supplier;

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
                'name' => 'supplychain.suppliers.manage',
                'display_name' => 'Manage Suppliers'
            ],
            [
                'name' => 'supplychain.suppliers.view',
                'display_name' => 'View Suppliers'
            ],
            [
                'name' => 'supplychain.manage',
                'display_name' => 'Manage Supply Chain'
            ],
            [
                'name' => 'supplychain.view',
                'display_name' => 'View Supply Chain'
            ]
        ];

        $module = new Module();
        $module->module_name = 'supplychain';
        $module->description = 'User can view all Suppliers.';
        $module->saveQuietly();

        $module->permissions()->createMany($permissions);

        $all = ['supplychain.suppliers.manage', 'supplychain.suppliers.view', 'supplychain.manage', 'supplychain.view'];
        Permission::whereIn('name', $all)->update(['allowed_permissions' => Permission::ALL_NONE]);

        $companies = Company::all();

        // We will insert these for the new company from event listener
        foreach ($companies as $company) {
            Supplier::addModuleSetting($company);
        }

        Artisan::call('module:enable supplychain');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $module = Module::where('module_name', 'supplychain')->first();
        if (!$module) {
            return;
        }

        $permisisons = Permission::where('module_id', $module->id)->get()->pluck('id')->toArray();
        PermissionRole::whereIn('permission_id', $permisisons)->delete();

        Module::where('module_name', 'supplychain')->delete();
    }

};
