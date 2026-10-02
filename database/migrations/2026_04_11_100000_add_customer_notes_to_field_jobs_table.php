<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cleaning_jobs', function (Blueprint $table) {
            $table->text('customer_notes')->nullable()->after('technician_notes');
        });
    }

    public function down(): void
    {
        Schema::table('cleaning_jobs', function (Blueprint $table) {
            $table->dropColumn('customer_notes');
        });
    }
};
