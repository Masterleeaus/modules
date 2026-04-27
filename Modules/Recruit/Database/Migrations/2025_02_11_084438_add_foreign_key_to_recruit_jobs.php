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
            $table->unsignedInteger('recruiter_id')->nullable()->change();
            $table->foreign('recruiter_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('set null');

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
            $table->dropForeign(['recruiter_id']);

            // Change the column back to NOT NULL (if needed)
            $table->unsignedInteger('recruiter_id')->nullable(false)->change();
        });

        }
    }
};
