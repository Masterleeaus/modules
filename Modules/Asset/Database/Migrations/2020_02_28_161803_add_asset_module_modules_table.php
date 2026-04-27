<?php

use App\Models\Module;
use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {

        $module = Module::firstOrCreate(['module_name' => 'asset']);

        Permission::insert(
            [
                ['name' => 'add_asset', 'display_name' => 'Add Asset', 'module_id' => $module->id],
                ['name' => 'view_asset', 'display_name' => 'View Asset', 'module_id' => $module->id],
                ['name' => 'edit_asset', 'display_name' => 'Edit Asset', 'module_id' => $module->id],
                ['name' => 'delete_asset', 'display_name' => 'Delete Asset', 'module_id' => $module->id],
            ]
        );

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
};
