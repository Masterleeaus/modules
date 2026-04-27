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
use Modules\Purchase\Entities\PurchaseManagementSetting;

return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        $purchaseModule = \App\Models\Module::firstOrCreate(['module_name' => PurchaseManagementSetting::MODULE_NAME]);

        $permissionTypes = [
            ['name' => 'add_vendor', 'display_name' => 'Add Vendor', 'is_custom' => 1, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],
            ['name' => 'view_vendor', 'display_name' => 'View Vendor', 'is_custom' => 1, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],
            ['name' => 'edit_vendor', 'display_name' => 'Edit Vendor', 'is_custom' => 1, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],
            ['name' => 'delete_vendor', 'display_name' => 'Delete Vendor', 'is_custom' => 1, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],

            ['name' => 'manage_vendor_contact', 'display_name' => 'Manage Vendor Contact', 'is_custom' => 1, 'allowed_permissions' => Permission::ALL_NONE],
            ['name' => 'manage_vendor_note', 'display_name' => 'Manage Vendor Note', 'is_custom' => 1, 'allowed_permissions' => Permission::ALL_NONE],

            ['name' => 'add_purchase_order', 'display_name' => 'Add Purchase Order', 'is_custom' => 1, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],
            ['name' => 'view_purchase_order', 'display_name' => 'View Purchase Order', 'is_custom' => 1, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],
            ['name' => 'edit_purchase_order', 'display_name' => 'Edit Purchase Order', 'is_custom' => 1, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],
            ['name' => 'delete_purchase_order', 'display_name' => 'Delete Purchase Order', 'is_custom' => 1, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],

            ['name' => 'add_bill', 'display_name' => 'Add Bill', 'is_custom' => 1, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],
            ['name' => 'view_bill', 'display_name' => 'View Bill', 'is_custom' => 1, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],
            ['name' => 'edit_bill', 'display_name' => 'Edit Bill', 'is_custom' => 1, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],
            ['name' => 'delete_bill', 'display_name' => 'Delete Bill', 'is_custom' => 1, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],

            ['name' => 'add_vendor_payment', 'display_name' => 'Add Vendor Payment', 'is_custom' => 1, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],
            ['name' => 'view_vendor_payment', 'display_name' => 'View Vendor Payment', 'is_custom' => 1, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],
            ['name' => 'edit_vendor_payment', 'display_name' => 'Edit Vendor Payment', 'is_custom' => 1, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],
            ['name' => 'delete_vendor_payment', 'display_name' => 'Delete Vendor Payment', 'is_custom' => 1, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],

            ['name' => 'add_vendor_credit', 'display_name' => 'Add Vendor Credit', 'is_custom' => 1, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],
            ['name' => 'view_vendor_credit', 'display_name' => 'View Vendor Credit', 'is_custom' => 1, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],
            ['name' => 'edit_vendor_credit', 'display_name' => 'Edit Vendor Credit', 'is_custom' => 1, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],
            ['name' => 'delete_vendor_credit', 'display_name' => 'Delete Vendor Credit', 'is_custom' => 1, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],

            ['name' => 'add_inventory', 'display_name' => 'Add Inventory', 'is_custom' => 1, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],
            ['name' => 'view_inventory', 'display_name' => 'View Inventory', 'is_custom' => 1, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],
            ['name' => 'edit_inventory', 'display_name' => 'Edit Inventory', 'is_custom' => 1, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],
            ['name' => 'delete_inventory', 'display_name' => 'Delete Inventory', 'is_custom' => 1, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],

            ['name' => 'manage_stock_adjustment', 'display_name' => 'Manage Stock Adjustment', 'is_custom' => 1, 'allowed_permissions' => Permission::ALL_NONE],
            ['name' => 'manage_inventory_adjustment', 'display_name' => 'Manage Inventory Adjustment', 'is_custom' => 1, 'allowed_permissions' => Permission::ALL_NONE],
            ['name' => 'view_order_report', 'display_name' => 'View Order Report', 'is_custom' => 1, 'allowed_permissions' => Permission::ALL_NONE],
            ['name' => 'view_vendor_report', 'display_name' => 'View Vendor Report', 'is_custom' => 1, 'allowed_permissions' => Permission::ALL_NONE],
            ['name' => 'link_order_bank_account', 'display_name' => 'Link Order Bank Account', 'is_custom' => 1, 'allowed_permissions' => Permission::ALL_NONE],
            ['name' => 'view_inventory_valuation_summary', 'display_name' => 'View Inventory Valuation Summary', 'is_custom' => 1, 'allowed_permissions' => Permission::ALL_NONE],
            ['name' => 'view_inventory_summary', 'display_name' => 'View Inventory Summary', 'is_custom' => 1, 'allowed_permissions' => Permission::ALL_NONE],
        ];

        $companies = Company::all();

        foreach ($companies as $company) {
            $roles = ['employee', 'admin'];
            ModuleSetting::createRoleSettingEntry(PurchaseManagementSetting::MODULE_NAME, $roles, $company);
        }

        foreach ($permissionTypes as $key => $permissionType) {

            $permission = Permission::firstOrCreate([
                'name' => $permissionType['name'],
                'display_name' => $permissionType['display_name'],
                'is_custom' => $permissionType['is_custom'],
                'module_id' => $purchaseModule->id,
                'allowed_permissions' => $permissionType['allowed_permissions'],
            ]);


            foreach ($companies as $company) {

                $role = Role::where('name', 'admin')
                    ->where('company_id', $company->id)
                    ->first();

                if ($role) {
                    $permissionData = PermissionRole::where('permission_id', $permission->id)
                        ->where('role_id', $role->id)->where('permission_type_id', 4)->first();

                    if (is_null($permissionData)) {
                        $permissionRole = new PermissionRole();
                        $permissionRole->permission_id = $permission->id;
                        $permissionRole->role_id = $role->id;
                        $permissionRole->permission_type_id = 4;
                        $permissionRole->save();
                    }
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
     *
     * @return void
     */
    public function down()
    {
        Module::where('module_name', PurchaseManagementSetting::MODULE_NAME)->delete();

        $moduleSettings = ModuleSetting::where('module_name', PurchaseManagementSetting::MODULE_NAME)->get();

        foreach ($moduleSettings as $moduleSetting) {
            $moduleSetting->delete();
        }
    }

};
