<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Seeds the `purchase_module` subscription-package feature for all existing
 * packages so that companies can enable/disable the Purchase module via their
 * subscription plan.
 *
 * Feature key: purchase_module
 *   – checked via hasFeature('purchase_module') in middleware / controllers
 *   – basic PO creation available in standard plans
 *   – supplier management, auto-reorder, invoice matching in premium plans
 */
return new class extends Migration {

    private string $feature = 'purchase_module';

    public function up(): void
    {
        if (!Schema::hasTable('subscription_packages') || !Schema::hasTable('subscription_package_features')) {
            return;
        }

        $packages = DB::table('subscription_packages')->pluck('id');

        foreach ($packages as $packageId) {
            $alreadyExists = DB::table('subscription_package_features')
                ->where('subscription_package_id', $packageId)
                ->where('feature', $this->feature)
                ->exists();

            if (!$alreadyExists) {
                DB::table('subscription_package_features')->insert([
                    'id'                      => (string) Str::uuid(),
                    'subscription_package_id' => $packageId,
                    'feature'                 => $this->feature,
                    'company_id'              => null,
                    'created_at'              => now(),
                    'updated_at'              => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('subscription_package_features')) {
            return;
        }

        DB::table('subscription_package_features')
            ->where('feature', $this->feature)
            ->delete();
    }
};
