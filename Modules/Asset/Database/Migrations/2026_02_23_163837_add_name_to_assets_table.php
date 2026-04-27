<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('assets') && !Schema::hasColumn('assets', 'name')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->string('name', 191)->nullable()->after('id');
            });
        }

        // Serial number should be optional for real-world equipment.
        if (Schema::hasTable('assets') && Schema::hasColumn('assets', 'serial_number')) {
            // Make nullable if it isn't already. (No-op on some MySQL configs if already nullable.)
            Schema::table('assets', function (Blueprint $table) {
                // If you have doctrine/dbal installed you can uncomment the next line to enforce nullable.
                // $table->string('serial_number', 191)->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('assets') && Schema::hasColumn('assets', 'name')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->dropColumn('name');
            });
        }
        // We do not force serial_number back to NOT NULL in down().
    }
};
