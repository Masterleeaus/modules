<?php

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $all = [
            'view_asset',
            'edit_asset',
            'delete_asset',
        ];

        Permission::whereIn('name', $all)->update(['allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5]);

        $allNonePermission = [
            'add_asset',
            'add_assets_type',
            'edit_assets_type',
            'view_assets_type',
            'delete_assets_type',
            'edit_assets_history',
            'delete_assets_history',
            'view_assets_history',
        ];

        Permission::whereIn('name', $allNonePermission)->update(['allowed_permissions' => Permission::ALL_NONE]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
