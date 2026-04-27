<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds cleaning-business-specific columns to the assets table:
 * - purchase_date        : when the equipment was purchased
 * - warranty_expiry_date : quick-access warranty expiry (asset_warranties has full detail)
 * - condition            : Good / Needs Service / Out of Order
 * - assigned_to_user_id  : cleaner the asset is currently assigned to
 * - depreciation_method  : straight-line / reducing-balance
 * - depreciation_value   : annual depreciation amount or percentage
 * - written_off_at       : disposal / write-off date
 * - written_off_reason   : reason for disposal
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('assets')) {
            return;
        }

        Schema::table('assets', function (Blueprint $table) {
            if (!Schema::hasColumn('assets', 'purchase_date')) {
                $table->date('purchase_date')->nullable()->after('serial_number');
            }
            if (!Schema::hasColumn('assets', 'warranty_expiry_date')) {
                $table->date('warranty_expiry_date')->nullable()->after('purchase_date');
            }
            if (!Schema::hasColumn('assets', 'condition')) {
                $table->enum('condition', ['good', 'needs_service', 'out_of_order'])
                    ->default('good')
                    ->after('status');
            }
            if (!Schema::hasColumn('assets', 'assigned_to_user_id')) {
                $table->unsignedInteger('assigned_to_user_id')->nullable()->after('condition');
                $table->foreign('assigned_to_user_id')
                    ->references('id')->on('users')
                    ->onDelete('SET NULL')
                    ->onUpdate('cascade');
            }
            if (!Schema::hasColumn('assets', 'depreciation_method')) {
                $table->string('depreciation_method', 30)->nullable()->after('value');
            }
            if (!Schema::hasColumn('assets', 'depreciation_value')) {
                $table->decimal('depreciation_value', 15, 4)->nullable()->after('depreciation_method');
            }
            if (!Schema::hasColumn('assets', 'written_off_at')) {
                $table->date('written_off_at')->nullable()->after('depreciation_value');
            }
            if (!Schema::hasColumn('assets', 'written_off_reason')) {
                $table->text('written_off_reason')->nullable()->after('written_off_at');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('assets')) {
            return;
        }

        Schema::table('assets', function (Blueprint $table) {
            $columns = [
                'purchase_date',
                'warranty_expiry_date',
                'condition',
                'assigned_to_user_id',
                'depreciation_method',
                'depreciation_value',
                'written_off_at',
                'written_off_reason',
            ];

            // Drop FK before column
            if (Schema::hasColumn('assets', 'assigned_to_user_id')) {
                $table->dropForeign(['assigned_to_user_id']);
            }

            foreach ($columns as $col) {
                if (Schema::hasColumn('assets', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
