<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('recruit_job_applications')) {
        Schema::table('recruit_job_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('recruit_job_applications', 'total_experience')) {
                $table->enum('total_experience', ['fresher', '1-2', '3-4', '5-6', '7-8', '9-10', '11-12', '13-14', 'over-15'])->default(null)->nullable()->after('gender');
            }
            if (!Schema::hasColumn('recruit_job_applications', 'current_location')) {
                $table->string('current_location')->nullable()->after('total_experience');
            }
            if (!Schema::hasColumn('recruit_job_applications', 'current_ctc')) {
                $table->string('current_ctc')->nullable()->after('current_location');
            }
            if (!Schema::hasColumn('recruit_job_applications', 'expected_ctc')) {
                $table->string('expected_ctc')->nullable()->after('current_ctc');
            }
            if (!Schema::hasColumn('recruit_job_applications', 'notice_period')) {
                $table->enum('notice_period', ['15', '30', '45', '60', '75', '90', 'over-90'])->default(null)->nullable()->after('expected_ctc');
            }
        });
    }

        DB::statement('ALTER TABLE `recruit_job_applications` CHANGE `current_ctc` `current_ctc` INT(11) NULL DEFAULT NULL');

        if (Schema::hasTable('recruit_settings')) {
        Schema::table('recruit_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('recruit_settings', 'application_restriction')) {
                $table->integer('application_restriction')->default('180');
            }
        });
    }

        DB::statement('ALTER TABLE `recruit_job_applications` CHANGE `email` `email` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL');

        if (Schema::hasTable('recruit_settings')) {
        Schema::table('recruit_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('recruit_settings', 'career_site')) {
                $table->enum('career_site', ['yes', 'no'])->default('yes')->after('company_website');
            }
        });
    }

        DB::statement('ALTER TABLE `recruit_jobs` CHANGE `end_date` `end_date` DATETIME NULL');
        DB::statement('ALTER TABLE `recruit_job_offer_letter` ADD CONSTRAINT `recruit_job_offer_letter_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
