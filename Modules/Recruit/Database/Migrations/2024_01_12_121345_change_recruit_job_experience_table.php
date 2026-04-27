<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            DB::statement("ALTER TABLE `recruit_jobs` DROP FOREIGN KEY `recruit_jobs_recruit_work_experience_id_foreign`");
            DB::statement("ALTER TABLE `recruit_jobs` ADD CONSTRAINT `recruit_jobs_recruit_work_experience_id_foreign` FOREIGN KEY (`recruit_work_experience_id`) REFERENCES recruit_work_experiences(id) ON DELETE SET NULL ON UPDATE CASCADE");

        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }

        try {
            DB::statement("ALTER TABLE `recruit_jobs` DROP FOREIGN KEY `recruit_jobs_department_id_foreign`");
            DB::statement("ALTER TABLE `recruit_jobs` ADD  CONSTRAINT `recruit_jobs_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES teams(id) ON DELETE SET NULL ON UPDATE CASCADE");

        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }

        try {
            DB::statement("ALTER TABLE `recruit_jobs` DROP FOREIGN KEY `recruit_jobs_recruit_job_category_id_foreign`");
            DB::statement("ALTER TABLE recruit_jobs ADD  CONSTRAINT recruit_jobs_recruit_job_category_id_foreign FOREIGN KEY (recruit_job_category_id) REFERENCES recruit_job_categories(id) ON DELETE SET NULL ON UPDATE CASCADE");

        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }

        try {
            DB::statement("ALTER TABLE `recruit_jobs` DROP FOREIGN KEY `recruit_jobs_recruit_job_sub_category_id_foreign`");
            DB::statement("ALTER TABLE recruit_jobs ADD  CONSTRAINT recruit_jobs_recruit_job_sub_category_id_foreign FOREIGN KEY (recruit_job_sub_category_id) REFERENCES recruit_job_sub_categories(id) ON DELETE SET NULL ON UPDATE CASCADE");

        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }

        try {
            DB::statement("ALTER TABLE `recruit_jobs` DROP FOREIGN KEY `recruit_jobs_recruit_job_type_id_foreign`");
            DB::statement("ALTER TABLE recruit_jobs ADD  CONSTRAINT recruit_jobs_recruit_job_type_id_foreign FOREIGN KEY (recruit_job_type_id) REFERENCES recruit_job_types(id) ON DELETE SET NULL ON UPDATE CASCADE");

        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }


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
