<?php

use App\Models\Permission;
use App\Models\PermissionType;
use App\Models\Role;
use App\Models\UserPermission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Asset\Entities\AssetSetting;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('assets', 'value')) {
            if (Schema::hasTable('assets')) {
        Schema::table('assets', function (Blueprint $table) {

                if (!Schema::hasColumn('assets', 'value')) {
                    $table->string('value')->nullable();
                }
                if (!Schema::hasColumn('assets', 'location')) {
                    $table->string('location')->nullable();
                }

                if (!Schema::hasColumn('assets', 'added_by')) {
                    $table->integer('added_by')->unsigned()->nullable();
                }
                $table->foreign('added_by')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('cascade');

                if (!Schema::hasColumn('assets', 'last_updated_by')) {
                    $table->integer('last_updated_by')->unsigned()->nullable();
                }
                $table->foreign('last_updated_by')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('cascade');
        });
    }
        }

        $assetModule = \App\Models\Module::where('module_name', AssetSetting::MODULE_NAME)->first();

        $admins = Role::join('role_user', 'roles.id', '=', 'role_user.role_id')
            ->where('name', 'admin')
            ->get();

        $customPermissions = [
            'add_assets_type',
            'edit_assets_type',
            'view_assets_type',
            'delete_assets_type',
            'edit_assets_history',
            'delete_assets_history',
            'view_assets_history',
        ];

        foreach ($customPermissions as $permission) {
            $perm = Permission::firstOrCreate([
                'name' => $permission,
                'display_name' => ucwords(str_replace('_', ' ', $permission)),
                'is_custom' => 1,
                'module_id' => $assetModule->id,
            ]);

            foreach ($admins as $item) {
                UserPermission::firstOrCreate(
                    [
                        'user_id' => $item->user_id,
                        'permission_id' => $perm->id,
                        'permission_type_id' => PermissionType::ALL,
                    ]
                );
            }
        }

        $regularPermissions = [
            'add_asset',
            'view_asset',
            'edit_asset',
            'delete_asset',
        ];

        foreach ($regularPermissions as $permission) {
            $perm = Permission::where('name', $permission)->first();

            foreach ($admins as $item) {
                UserPermission::firstOrCreate(
                    [
                        'user_id' => $item->user_id,
                        'permission_id' => $perm->id,
                        'permission_type_id' => PermissionType::ALL,
                    ]
                );
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
        $assetModule = \App\Models\Module::where(['module_name' => 'assets'])->first();
        Permission::where('module_id', $assetModule->id)->delete();
    }
};
