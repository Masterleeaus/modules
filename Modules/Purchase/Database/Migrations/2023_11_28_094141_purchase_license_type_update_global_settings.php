<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('purchase_management_settings') || Schema::hasColumn('purchase_management_settings', 'license_type')) {
            return;
        }

        $afterColumn = Schema::hasColumn('purchase_management_settings', 'supported_until') ? 'supported_until' : null;

        Schema::table('purchase_management_settings', function (Blueprint $table) use ($afterColumn) {
            $column = $table->string('license_type', 20)->nullable();
            if ($afterColumn) {
                $column->after($afterColumn);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('purchase_management_settings') || ! Schema::hasColumn('purchase_management_settings', 'license_type')) {
            return;
        }

        Schema::table('purchase_management_settings', function (Blueprint $table) {
            $table->dropColumn('license_type');
        });
    }
};
