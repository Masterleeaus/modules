<?php

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Adds granular purchase-workflow permissions required by the audit checklist:
 *
 *  view_purchases          – list / read purchase orders
 *  create_purchase_orders  – draft and save POs
 *  approve_purchases       – approve POs above threshold
 *  receive_goods           – mark PO goods as received & update stock
 *
 * Note: We use the hard-coded module name string 'purchase' rather than
 * importing PurchaseManagementSetting::MODULE_NAME to avoid a class-load
 * failure if the entity is not available when early migrations run.
 */
return new class extends Migration {

    private array $permissions = [
        ['name' => 'view_purchases',         'display_name' => 'View Purchases'],
        ['name' => 'create_purchase_orders', 'display_name' => 'Create Purchase Orders'],
        ['name' => 'approve_purchases',      'display_name' => 'Approve Purchases'],
        ['name' => 'receive_goods',          'display_name' => 'Receive Goods'],
    ];

    /** Hard-coded to avoid a class-load dependency at migration time. */
    private string $moduleName = 'purchase';

    public function up(): void
    {
        if (!Schema::hasTable('permissions')) {
            return;
        }

        // Resolve the purchase module ID (may be null if modules table is absent)
        $moduleId = null;
        if (Schema::hasTable('modules')) {
            $module = DB::table('modules')
                ->where('module_name', $this->moduleName)
                ->first();
            $moduleId = $module?->id;
        }

        $needsGuard = Schema::hasColumn('permissions', 'guard_name');

        foreach ($this->permissions as $perm) {
            if (DB::table('permissions')->where('name', $perm['name'])->exists()) {
                continue;
            }

            $row = [
                'name'                => $perm['name'],
                'display_name'        => $perm['display_name'],
                'is_custom'           => 1,
                'module_id'           => $moduleId,
                'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5,
                'created_at'          => now(),
                'updated_at'          => now(),
            ];

            if ($needsGuard) {
                $row['guard_name'] = 'web';
            }

            DB::table('permissions')->insert($row);
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('permissions')) {
            return;
        }

        DB::table('permissions')
            ->whereIn('name', array_column($this->permissions, 'name'))
            ->delete();
    }
};
