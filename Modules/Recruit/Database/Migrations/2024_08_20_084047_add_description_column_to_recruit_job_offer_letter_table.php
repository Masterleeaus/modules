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
        if (Schema::hasTable('recruit_job_offer_letter')) {
        Schema::table('recruit_job_offer_letter', function (Blueprint $table) {
            if (!Schema::hasColumn('recruit_job_offer_letter', 'description')) {
                $table->longText('description')->nullable();
            }
        });
    }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recruit_job_offer_letter', function (Blueprint $table) {

        });
    }
};
