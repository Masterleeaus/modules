<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    private array $newPermissions = [
        ['name' => 'view_qc_reports',      'display_name' => 'View QC Reports'],
        ['name' => 'manage_qc_templates',  'display_name' => 'Manage QC Templates'],
        ['name' => 'trigger_reclean',      'display_name' => 'Trigger Re-clean'],
        ['name' => 'view_cleaner_ratings', 'display_name' => 'View Cleaner Ratings'],
    ];

    public function up(): void
    {
        // 1. Register new permissions in the permissions table (Entrust / Worksuite style).
        if (Schema::hasTable('permissions')) {
            // Determine whether permissions table uses module_id column.
            $hasModuleId = Schema::hasColumn('permissions', 'module_id');

            // Resolve the module id for quality_control if available.
            $moduleId = null;

            if ($hasModuleId && Schema::hasTable('modules') && Schema::hasColumn('modules', 'module_name')) {
                $module = DB::table('modules')->where('module_name', 'quality_control')->first();
                $moduleId = $module ? $module->id : null;
            }

            foreach ($this->newPermissions as $perm) {
                $row = [
                    'name'         => $perm['name'],
                    'display_name' => $perm['display_name'],
                    'guard_name'   => 'web',
                    'updated_at'   => now(),
                    'created_at'   => now(),
                ];

                if ($hasModuleId && $moduleId !== null) {
                    $row['module_id'] = $moduleId;
                }

                DB::table('permissions')->updateOrInsert(
                    ['name' => $perm['name']],
                    $row
                );
            }
        }

        // 2. Register quality_control in subscription_package_features
        //    so it appears in Superadmin package management.
        if (!Schema::hasTable('subscription_package_features') || !Schema::hasTable('subscription_packages')) {
            return;
        }

        $cols    = Schema::getColumnListing('subscription_package_features');
        $hasUuid = in_array('id', $cols, true);

        if (!in_array('feature', $cols, true)) {
            return;
        }

        $packages = DB::table('subscription_packages')->get();

        foreach ($packages as $package) {
            $exists = DB::table('subscription_package_features')
                ->where('subscription_package_id', $package->id)
                ->where('feature', 'quality_control')
                ->exists();

            if (!$exists) {
                $row = [
                    'feature'                 => 'quality_control',
                    'subscription_package_id' => $package->id,
                    'created_at'              => now(),
                    'updated_at'              => now(),
                ];

                if ($hasUuid) {
                    $row['id'] = Str::uuid()->toString();
                }

                DB::table('subscription_package_features')->insert($row);
            }
        }

        // 3. Ensure the modules table entry is up-to-date.
        if (Schema::hasTable('modules') && Schema::hasColumn('modules', 'module_name')) {
            DB::table('modules')->updateOrInsert(
                ['module_name' => 'quality_control'],
                [
                    'description' => 'Quality control checklists, cleaner ratings and re-clean workflow',
                    'is_superadmin' => 0,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('permissions')) {
            $names = array_column($this->newPermissions, 'name');
            DB::table('permissions')->whereIn('name', $names)->delete();
        }

        if (Schema::hasTable('subscription_package_features')) {
            DB::table('subscription_package_features')
                ->where('feature', 'quality_control')
                ->delete();
        }
    }
};
