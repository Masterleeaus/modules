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
            DB::statement("ALTER TABLE `recruit_job_applications` DROP FOREIGN KEY `recruit_job_applications_application_source_id_foreign`; ALTER TABLE `recruit_job_applications` ADD CONSTRAINT `recruit_job_applications_application_source_id_foreign` FOREIGN KEY (`application_source_id`) REFERENCES `application_sources`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;");

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
