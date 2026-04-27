<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('recruit_jobs')) {
        Schema::table('recruit_jobs', function (Blueprint $table) {
            if (!Schema::hasColumn('recruit_jobs', 'is_currentctc_require')) {
                $table->boolean('is_currentctc_require')->default(false);
            }
            if (!Schema::hasColumn('recruit_jobs', 'is_expectedctc_require')) {
                $table->boolean('is_expectedctc_require')->default(false);
            }
        });
    }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('recruit_jobs')) {
        Schema::table('recruit_jobs', function (Blueprint $table) {
            if (Schema::hasColumn('recruit_jobs', 'is_currentctc_require')) {
                $table->dropColumn('is_currentctc_require');
            }
            if (Schema::hasColumn('recruit_jobs', 'is_expectedctc_require')) {
                $table->dropColumn('is_expectedctc_require');
            }
        });
    }
    }
};
