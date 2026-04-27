<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds procurement-specific columns to the existing `suppliers` table.
 *
 * The suppliers table is owned by the Suppliers module; these columns are
 * required by the Purchase module's integration (credit-limit enforcement,
 * ABN for GST, preferred payment terms, soft-delete support).
 */
return new class extends Migration {

    public function up(): void
    {
        if (!Schema::hasTable('suppliers')) {
            return;
        }

        Schema::table('suppliers', function (Blueprint $table) {
            if (!Schema::hasColumn('suppliers', 'abn')) {
                $table->string('abn')->nullable()->after('email');
            }

            if (!Schema::hasColumn('suppliers', 'address')) {
                $table->string('address')->nullable()->after('abn');
            }

            if (!Schema::hasColumn('suppliers', 'payment_terms')) {
                $table->string('payment_terms')->default('30_days')->after('address');
            }

            if (!Schema::hasColumn('suppliers', 'credit_limit')) {
                $table->decimal('credit_limit', 10, 2)->nullable()->after('payment_terms');
            }

            if (!Schema::hasColumn('suppliers', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('credit_limit');
            }

            if (!Schema::hasColumn('suppliers', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('suppliers')) {
            return;
        }

        Schema::table('suppliers', function (Blueprint $table) {
            $toDrop = [];

            foreach (['abn', 'address', 'payment_terms', 'credit_limit', 'is_active', 'deleted_at'] as $col) {
                if (Schema::hasColumn('suppliers', $col)) {
                    $toDrop[] = $col;
                }
            }

            if (!empty($toDrop)) {
                $table->dropColumn($toDrop);
            }
        });
    }
};
