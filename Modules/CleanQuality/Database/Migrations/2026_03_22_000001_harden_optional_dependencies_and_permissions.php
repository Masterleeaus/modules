<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('permissions')) {
            foreach (['view_quality_control', 'add_quality_control', 'edit_quality_control', 'delete_quality_control'] as $name) {
                DB::table('permissions')->updateOrInsert(
                    ['name' => $name],
                    ['guard_name' => 'web', 'updated_at' => now(), 'created_at' => now()]
                );
            }
        }

        if (Schema::hasTable('modules') && Schema::hasColumn('modules', 'module_name')) {
            DB::table('modules')->updateOrInsert(
                ['module_name' => 'quality_control'],
                ['description' => 'Quality Control', 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }

    public function down(): void
    {
        // Non-destructive hardening migration.
    }
};
