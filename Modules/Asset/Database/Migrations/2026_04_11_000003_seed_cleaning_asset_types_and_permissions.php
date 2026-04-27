<?php

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;
use Modules\Asset\Entities\AssetSetting;

/**
 * Seeds the cleaning-business asset types and adds the manage_maintenance permission.
 */
return new class extends Migration
{
    /**
     * Cleaning-specific asset types to seed when asset_types table is empty
     * or does not yet contain these types.
     */
    private array $cleaningAssetTypes = [
        'Vacuum Cleaner',
        'Steam Mop',
        'Carpet Extractor',
        'Pressure Washer',
        'Vehicle',
        'Chemical Sprayer',
        'Floor Scrubber',
        'Window Cleaning Kit',
    ];

    public function up(): void
    {
        // ── Seed cleaning asset types ──────────────────────────────────────────
        if (\Illuminate\Support\Facades\Schema::hasTable('asset_types')) {
            foreach ($this->cleaningAssetTypes as $typeName) {
                \Illuminate\Support\Facades\DB::table('asset_types')
                    ->insertOrIgnore(['name' => $typeName, 'created_at' => now(), 'updated_at' => now()]);
            }
        }

        // ── Add manage_maintenance permission ──────────────────────────────────
        if (!\Illuminate\Support\Facades\Schema::hasTable('permissions')) {
            return;
        }

        $module = \App\Models\Module::where('module_name', AssetSetting::MODULE_NAME)->first();
        if (!$module) {
            return;
        }

        Permission::firstOrCreate(
            ['name' => 'manage_maintenance'],
            [
                'display_name' => 'Manage Maintenance',
                'module_id'    => $module->id,
                'is_custom'    => 0,
            ]
        );
    }

    public function down(): void
    {
        // Remove seeded asset types (only if they have no assets attached)
        if (\Illuminate\Support\Facades\Schema::hasTable('asset_types')) {
            foreach ($this->cleaningAssetTypes as $typeName) {
                $type = \Illuminate\Support\Facades\DB::table('asset_types')
                    ->where('name', $typeName)->first();
                if ($type) {
                    $used = \Illuminate\Support\Facades\DB::table('assets')
                        ->where('asset_type_id', $type->id)->exists();
                    if (!$used) {
                        \Illuminate\Support\Facades\DB::table('asset_types')
                            ->where('id', $type->id)->delete();
                    }
                }
            }
        }

        if (\Illuminate\Support\Facades\Schema::hasTable('permissions')) {
            Permission::where('name', 'manage_maintenance')->delete();
        }
    }
};
