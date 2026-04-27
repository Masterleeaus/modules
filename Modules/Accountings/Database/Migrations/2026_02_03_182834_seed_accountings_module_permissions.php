<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        $module = DB::table('modules')->where('module_name', 'accountings')->first();
        if (!$module) {
            $id = DB::table('modules')->insertGetId([
                'module_name' => 'accountings',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $id = $module->id;
        }

        $hasGuardName = Schema::hasColumn('permissions', 'guard_name');

        $perms = ['accountings.view', 'accountings.create', 'accountings.edit', 'accountings.delete'];
        foreach ($perms as $p) {
            $perm = DB::table('permissions')->where('name', $p)->first();
            if (!$perm) {
                $row = [
                    'name' => $p,
                    'module_id' => $id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if ($hasGuardName) {
                    $row['guard_name'] = 'web';
                }

                DB::table('permissions')->insert($row);
            }
        }
    }

    public function down() {}
};
