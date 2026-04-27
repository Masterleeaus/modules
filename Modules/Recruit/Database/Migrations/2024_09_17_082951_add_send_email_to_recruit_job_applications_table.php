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
        if (Schema::hasTable('recruit_job_applications')) {
        Schema::table('recruit_job_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('recruit_job_applications', 'send_email')) {
                $table->boolean('send_email')->nullable();
            }
        });
    }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recruit_job_applications', function (Blueprint $table) {

        });
    }
};
