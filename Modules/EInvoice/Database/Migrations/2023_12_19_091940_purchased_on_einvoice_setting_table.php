<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('e_invoice_settings') || Schema::hasColumn('e_invoice_settings', 'purchased_on')) {
            return;
        }

        $afterColumn = Schema::hasColumn('e_invoice_settings', 'supported_until') ? 'supported_until' : null;

        Schema::table('e_invoice_settings', function (Blueprint $table) use ($afterColumn) {
            $column = $table->timestamp('purchased_on')->nullable();
            if ($afterColumn) {
                $column->after($afterColumn);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('e_invoice_settings') || ! Schema::hasColumn('e_invoice_settings', 'purchased_on')) {
            return;
        }

        Schema::table('e_invoice_settings', function (Blueprint $table) {
            $table->dropColumn('purchased_on');
        });
    }
};
