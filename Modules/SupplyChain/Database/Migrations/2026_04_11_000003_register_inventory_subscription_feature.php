<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Register 'inventory' as a subscription package feature for all existing packages.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('subscription_packages') || !Schema::hasTable('subscription_package_features')) {
            return;
        }

        $packages = DB::table('subscription_packages')->pluck('id');

        foreach ($packages as $packageId) {
            $exists = DB::table('subscription_package_features')
                ->where('subscription_package_id', $packageId)
                ->where('feature', 'inventory')
                ->exists();

            if (!$exists) {
                DB::table('subscription_package_features')->insert([
                    'id'                      => (string) Str::uuid(),
                    'subscription_package_id' => $packageId,
                    'feature'                 => 'inventory',
                    'company_id'              => null,
                    'created_at'              => now(),
                    'updated_at'              => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('subscription_package_features')) {
            DB::table('subscription_package_features')
                ->where('feature', 'inventory')
                ->delete();
        }
    }
};
