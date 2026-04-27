<?php

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        if (! Schema::hasTable('recruit_application_status_categories')) {
            Schema::create('recruit_application_status_categories', function (Blueprint $table) {
                $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id')->nullable()->index();
                $table->string('name');
                $table->timestamps();
            });
        }

        if (! Schema::hasColumn('recruit_job_applications', 'job_id') && ! Schema::hasColumn('recruit_job_applications', 'recruit_job_id')) {
            Schema::table('recruit_job_applications', function (Blueprint $table) {
                $table->bigInteger('job_id')->unsigned();
                $table->foreign('job_id')->references('id')->on('recruit_jobs')->onUpdate('cascade')->onDelete('cascade');
            });
        }

        if (! Schema::hasColumn('recruit_job_applications', 'file_id') && ! Schema::hasColumn('recruit_job_applications', 'recruit_job_file_id')) {
            Schema::table('recruit_job_applications', function (Blueprint $table) {
                $table->integer('file_id')->unsigned()->nullable();
                $table->foreign('file_id')->references('id')->on('recruit_job_files')->onDelete('cascade')->onUpdate('cascade');
            });
        }

        if (! Schema::hasColumn('recruit_application_status', 'category_id')) {
            Schema::table('recruit_application_status', function (Blueprint $table) {
                $table->bigInteger('category_id')->unsigned()->nullable()->default(null);
                $table->foreign('category_id')->references('id')->on('recruit_application_status_categories')->onUpdate('cascade')->onDelete('cascade');
            });
        }

        if (! Schema::hasColumn('recruit_jobs', 'remote_job')) {
            Schema::table('recruit_jobs', function (Blueprint $table) {
                $table->enum('remote_job', ['yes', 'no'])->default('no')->nullable()->after('status');
                $table->enum('disclose_salary', ['yes', 'no'])->default('no')->nullable()->after('remote_job');
            });

            DB::statement('ALTER TABLE `recruit_job_applications` CHANGE `current_ctc` `current_ctc` DOUBLE NULL DEFAULT NULL');

            DB::statement('ALTER TABLE `recruit_job_applications` CHANGE `expected_ctc` `expected_ctc` DOUBLE NULL DEFAULT NULL');
        }

        if (! Schema::hasTable('recruit_job_categories')) {
            Schema::create('recruit_job_categories', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->text('category_name');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('recruit_job_sub_categories')) {
            Schema::create('recruit_job_sub_categories', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('recruit_job_category_id')->unsigned();
                $table->foreign('recruit_job_category_id')->references('id')->on('recruit_job_categories')->onUpdate('cascade')->onDelete('cascade');
                $table->text('sub_category_name');
                $table->timestamps();
            });
        }

        if (! Schema::hasColumn('recruit_jobs', 'recruit_job_sub_category_id')) {
            Schema::table('recruit_jobs', function (Blueprint $table) {
                $table->integer('recruit_job_sub_category_id')->unsigned()->nullable()->after('status');
                $table->foreign('recruit_job_sub_category_id')->references('id')->on('recruit_job_sub_categories')->onUpdate('cascade')->onDelete('cascade');
                $table->integer('recruit_job_category_id')->unsigned()->nullable()->after('status');
                $table->foreign('recruit_job_category_id')->references('id')->on('recruit_job_categories')->onUpdate('cascade')->onDelete('cascade');
                $table->integer('currency_id')->unsigned()->nullable()->after('status');
                $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade')->onUpdate('cascade');
            });

        }

        if (! Schema::hasColumn('recruit_jobs', 'recruit_job_type_id')) {
            Schema::table('recruit_jobs', function (Blueprint $table) {
                DB::statement('ALTER TABLE `recruit_jobs` CHANGE `job_type_id` `recruit_job_type_id` BIGINT UNSIGNED NULL DEFAULT NULL;');
                $table->foreign('recruit_job_type_id')->references('id')->on('recruit_job_types')->onUpdate('cascade')->onDelete('cascade');
                $table->dropForeign('recruit_jobs_job_type_id_foreign');

                DB::statement('ALTER TABLE `recruit_jobs` CHANGE `work_experience_id` `recruit_work_experience_id` BIGINT UNSIGNED NULL DEFAULT NULL;');
                $table->foreign('recruit_work_experience_id')->references('id')->on('recruit_work_experiences')->onUpdate('cascade')->onDelete('cascade');
                $table->dropForeign('recruit_jobs_work_experience_id_foreign');
            });
        }

        if (! Schema::hasColumn('recruit_job_skills', 'recruit_job_id')) {
            Schema::table('recruit_job_skills', function (Blueprint $table) {
                DB::statement('ALTER TABLE `recruit_job_skills` CHANGE `job_id` `recruit_job_id` BIGINT UNSIGNED NOT NULL;');
                $table->foreign('recruit_job_id')->references('id')->on('recruit_jobs')->onDelete('cascade')->onUpdate('cascade');
                $table->dropForeign('recruit_job_skills_job_id_foreign');

                DB::statement('ALTER TABLE `recruit_job_skills` CHANGE `skill_id` `recruit_skill_id` INT UNSIGNED NOT NULL;');
                $table->foreign('recruit_skill_id')->references('id')->on('recruit_skills')->onDelete('cascade')->onUpdate('cascade');
                $table->dropForeign('recruit_job_skills_skill_id_foreign');
            });
        }

        if (! Schema::hasColumn('recruit_application_status', 'recruit_application_status_category_id')) {
            Schema::table('recruit_application_status', function (Blueprint $table) {
                DB::statement('ALTER TABLE `recruit_application_status` CHANGE `category_id` `recruit_application_status_category_id` BIGINT UNSIGNED NULL DEFAULT NULL;');
                $table->foreign('recruit_application_status_category_id', 'ras_recruit_application_status_category_id_foreign')->references('id')->on('recruit_application_status_categories')->onUpdate('cascade')->onDelete('cascade');
                $table->dropForeign('recruit_application_status_category_id_foreign');
            });
        }

        if (! Schema::hasColumn('recruit_job_files', 'recruit_job_id')) {
            Schema::table('recruit_job_files', function (Blueprint $table) {
                DB::statement('ALTER TABLE `recruit_job_files` CHANGE `job_id` `recruit_job_id` BIGINT UNSIGNED NOT NULL;');
                $table->foreign('recruit_job_id')->references('id')->on('recruit_jobs')->onDelete('cascade')->onUpdate('cascade');
                $table->dropForeign('recruit_job_files_job_id_foreign');
            });
        }

        if (! Schema::hasColumn('recruit_job_applications', 'recruit_job_id')) {
            Schema::table('recruit_job_applications', function (Blueprint $table) {
                DB::statement('ALTER TABLE `recruit_job_applications` CHANGE `job_id` `recruit_job_id` BIGINT UNSIGNED NOT NULL;');
                $table->foreign('recruit_job_id')->references('id')->on('recruit_jobs')->onUpdate('cascade')->onDelete('cascade');
                $table->dropForeign('recruit_job_applications_job_id_foreign');

                DB::statement('ALTER TABLE `recruit_job_applications` CHANGE `status_id` `recruit_application_status_id` INT UNSIGNED NULL DEFAULT NULL;');
                $table->foreign('recruit_application_status_id')->references('id')->on('recruit_application_status')->onUpdate('cascade')->onDelete('cascade');
                $table->dropForeign('recruit_job_applications_status_id_foreign');

                DB::statement('ALTER TABLE `recruit_job_applications` CHANGE `source_id` `application_source_id` INT UNSIGNED NULL DEFAULT NULL;');
                $table->foreign('application_source_id')->references('id')->on('application_sources')->onDelete('cascade')->onUpdate('cascade');
                $table->dropForeign('recruit_job_applications_source_id_foreign');

                DB::statement('ALTER TABLE `recruit_job_applications` CHANGE `file_id` `recruit_job_file_id` INT UNSIGNED NULL DEFAULT NULL;');
                $table->foreign('recruit_job_file_id')->references('id')->on('recruit_job_files')->onDelete('cascade')->onUpdate('cascade');
                $table->dropForeign('recruit_job_applications_file_id_foreign');
            });
        }

        if (! Schema::hasColumn('recruit_applicant_notes', 'recruit_job_application_id')) {
            Schema::table('recruit_applicant_notes', function (Blueprint $table) {
                DB::statement('ALTER TABLE `recruit_applicant_notes` CHANGE `job_application_id` `recruit_job_application_id` INT UNSIGNED NOT NULL;');
                $table->foreign('recruit_job_application_id')->references('id')->on('recruit_job_applications')->onUpdate('cascade')->onDelete('cascade');
                $table->dropForeign('recruit_applicant_notes_job_application_id_foreign');
            });
        }

        if (! Schema::hasColumn('recruit_application_skills', 'recruit_job_application_id')) {
            Schema::table('recruit_application_skills', function (Blueprint $table) {
                DB::statement('ALTER TABLE `recruit_application_skills` CHANGE `application_id` `recruit_job_application_id` INT UNSIGNED NOT NULL;');
                $table->foreign('recruit_job_application_id')->references('id')->on('recruit_job_applications')->onDelete('cascade')->onUpdate('cascade');
                $table->dropForeign('recruit_application_skills_application_id_foreign');

                DB::statement('ALTER TABLE `recruit_application_skills` CHANGE `skill_id` `recruit_skill_id` INT UNSIGNED NOT NULL;');
                $table->foreign('recruit_skill_id')->references('id')->on('recruit_skills')->onDelete('cascade')->onUpdate('cascade');
                $table->dropForeign('recruit_application_skills_skill_id_foreign');
            });
        }

        if (! Schema::hasColumn('recruit_jobboard_settings', 'recruit_application_status_id')) {
            Schema::table('recruit_jobboard_settings', function (Blueprint $table) {
                DB::statement('ALTER TABLE `recruit_jobboard_settings` CHANGE `board_column_id` `recruit_application_status_id` INT UNSIGNED NOT NULL;');
                $table->foreign('recruit_application_status_id')->references('id')->on('recruit_application_status')->onDelete('cascade')->onUpdate('cascade');
                $table->dropForeign('recruit_jobboard_settings_board_column_id_foreign');
            });
        }

        if (! Schema::hasColumn('recruit_interview_schedules', 'recruit_job_application_id')) {
            Schema::table('recruit_interview_schedules', function (Blueprint $table) {
                DB::statement('ALTER TABLE `recruit_interview_schedules` CHANGE `job_application_id` `recruit_job_application_id` INT UNSIGNED NOT NULL;');
                $table->foreign('recruit_job_application_id')->references('id')->on('recruit_job_applications')->onUpdate('cascade')->onDelete('cascade');
                $table->dropForeign('recruit_interview_schedules_job_application_id_foreign');

                DB::statement('ALTER TABLE `recruit_interview_schedules` CHANGE `stage_id` `recruit_interview_stage_id` INT UNSIGNED NULL DEFAULT NULL;');
                $table->foreign('recruit_interview_stage_id')->references('id')->on('recruit_interview_stages')->onDelete('cascade')->onUpdate('cascade');
                $table->dropForeign('recruit_interview_schedules_stage_id_foreign');
            });
        }

        if (! Schema::hasColumn('recruit_interview_employees', 'recruit_interview_schedule_id')) {
            Schema::table('recruit_interview_employees', function (Blueprint $table) {
                DB::statement('ALTER TABLE `recruit_interview_employees` CHANGE `interview_schedule_id` `recruit_interview_schedule_id` INT UNSIGNED NOT NULL;');
                $table->foreign('recruit_interview_schedule_id', 'rie_recruit_interview_schedule_id_foreign')->references('id')->on('recruit_interview_schedules')->onUpdate('cascade')->onDelete('cascade');
                $table->dropForeign('recruit_interview_employees_interview_schedule_id_foreign');
            });
        }

        if (! Schema::hasColumn('recruit_interview_comments', 'recruit_interview_schedule_id')) {
            Schema::table('recruit_interview_comments', function (Blueprint $table) {
                DB::statement('ALTER TABLE `recruit_interview_comments` CHANGE `interview_schedule_id` `recruit_interview_schedule_id` INT UNSIGNED NOT NULL;');
                $table->foreign('recruit_interview_schedule_id')->references('id')->on('recruit_interview_schedules')->onUpdate('cascade')->onDelete('cascade');
                $table->dropForeign('recruit_interview_comments_interview_schedule_id_foreign');
            });
        }

        if (! Schema::hasColumn('recruit_application_files', 'recruit_job_application_id')) {
            Schema::table('recruit_application_files', function (Blueprint $table) {
                DB::statement('ALTER TABLE `recruit_application_files` CHANGE `application_id` `recruit_job_application_id` INT UNSIGNED NOT NULL;');
                $table->foreign('recruit_job_application_id')->references('id')->on('recruit_job_applications')->onUpdate('cascade')->onDelete('cascade');
                $table->dropForeign('recruit_application_files_application_id_foreign');
            });
        }

        if (! Schema::hasColumn('recruit_job_offer_letter', 'recruit_job_id')) {
            Schema::table('recruit_job_offer_letter', function (Blueprint $table) {
                DB::statement('ALTER TABLE `recruit_job_offer_letter` CHANGE `job_app_id` `recruit_job_application_id` INT UNSIGNED NULL DEFAULT NULL;');
                $table->foreign('recruit_job_application_id')->references('id')->on('recruit_job_applications')
                    ->onUpdate('cascade')->onDelete('cascade');
                $table->dropForeign('recruit_job_offer_letter_job_app_id_foreign');

                DB::statement('ALTER TABLE `recruit_job_offer_letter` CHANGE `job_id` `recruit_job_id` BIGINT UNSIGNED NULL DEFAULT NULL;');
                $table->foreign('recruit_job_id')->references('id')->on('recruit_jobs')
                    ->onUpdate('cascade')->onDelete('cascade');
                $table->dropForeign('recruit_job_offer_letter_job_id_foreign');
            });
        }

        if (! Schema::hasColumn('recruit_job_histories', 'recruit_interview_schedule_id')) {
            Schema::table('recruit_job_histories', function (Blueprint $table) {
                DB::statement('ALTER TABLE `recruit_job_histories` CHANGE `job_id` `recruit_job_id` BIGINT UNSIGNED NULL DEFAULT NULL;');
                $table->foreign('recruit_job_id')->references('id')->on('recruit_jobs')
                    ->onUpdate('cascade')->onDelete('cascade');
                $table->dropForeign('recruit_job_histories_job_id_foreign');

                DB::statement('ALTER TABLE `recruit_job_histories` CHANGE `job_application_id` `recruit_job_application_id` INT UNSIGNED NULL DEFAULT NULL;');
                $table->foreign('recruit_job_application_id')->references('id')->on('recruit_job_applications')
                    ->onUpdate('cascade')->onDelete('cascade');
                $table->dropForeign('recruit_job_histories_job_application_id_foreign');

                DB::statement('ALTER TABLE `recruit_job_histories` CHANGE `offer_id` `recruit_job_offer_letter_id` INT UNSIGNED NULL DEFAULT NULL;');
                $table->foreign('recruit_job_offer_letter_id')->references('id')->on('recruit_job_offer_letter')
                    ->onUpdate('cascade')->onDelete('cascade');
                $table->dropForeign('recruit_job_histories_offer_id_foreign');

                DB::statement('ALTER TABLE `recruit_job_histories` CHANGE `interview_schedule_id` `recruit_interview_schedule_id` INT UNSIGNED NULL DEFAULT NULL;');
                $table->foreign('recruit_interview_schedule_id')->references('id')->on('recruit_interview_schedules')
                    ->onUpdate('cascade')->onDelete('cascade');
                $table->dropForeign('recruit_job_histories_interview_schedule_id_foreign');
            });
        }

        if (! Schema::hasColumn('recruit_interview_files', 'recruit_interview_schedule_id')) {
            Schema::table('recruit_interview_files', function (Blueprint $table) {
                DB::statement('ALTER TABLE `recruit_interview_files` CHANGE `interview_id` `recruit_interview_schedule_id` INT UNSIGNED NOT NULL;');
                $table->foreign('recruit_interview_schedule_id', 'rif_recruit_interview_schedule_id_foreign')->references('id')->on('recruit_interview_schedules')->onUpdate('cascade')->onDelete('cascade');
                $table->dropForeign('recruit_interview_files_interview_id_foreign');
            });
        }

        if (! Schema::hasColumn('recruit_interview_histories', 'recruit_interview_file_id')) {
            Schema::table('recruit_interview_histories', function (Blueprint $table) {
                DB::statement('ALTER TABLE `recruit_interview_histories` CHANGE `interview_schedule_id` `recruit_interview_schedule_id` INT UNSIGNED NULL DEFAULT NULL;');
                $table->foreign('recruit_interview_schedule_id', 'rih_recruit_interview_schedule_id_foreign')->references('id')->on('recruit_interview_schedules')->onUpdate('cascade')->onDelete('cascade');
                $table->dropForeign('recruit_interview_histories_interview_schedule_id_foreign');

                DB::statement('ALTER TABLE `recruit_interview_histories` CHANGE `file_id` `recruit_interview_file_id` INT UNSIGNED NULL DEFAULT NULL;');
                $table->foreign('recruit_interview_file_id')->references('id')->on('recruit_interview_files')->onUpdate('cascade')->onDelete('cascade');
                $table->dropForeign('recruit_interview_histories_file_id_foreign');
            });
        }

        if (! Schema::hasColumn('recruit_job_offer_files', 'recruit_job_offer_letter_id')) {
            Schema::table('recruit_job_offer_files', function (Blueprint $table) {
                DB::statement('ALTER TABLE `recruit_job_offer_files` CHANGE `job_offer_id` `recruit_job_offer_letter_id` INT UNSIGNED NOT NULL;');
                $table->foreign('recruit_job_offer_letter_id')->references('id')->on('recruit_job_offer_letter')
                    ->onUpdate('cascade')->onDelete('cascade');
                $table->dropForeign('recruit_job_offer_files_job_offer_id_foreign');
            });
        }

        if (! Schema::hasColumn('recruit_interview_evaluations', 'recruit_interview_stage_id')) {
            Schema::table('recruit_interview_evaluations', function (Blueprint $table) {
                DB::statement('ALTER TABLE `recruit_interview_evaluations` CHANGE `status_id` `recruit_recommendation_status_id` INT UNSIGNED NULL DEFAULT NULL;');
                $table->foreign('recruit_recommendation_status_id', 'rie_recruit_recommendation_status_id_foreign')->references('id')->on('recruit_recommendation_statuses')->onUpdate('cascade')->onDelete('cascade');
                $table->dropForeign('recruit_interview_evaluations_status_id_foreign');

                DB::statement('ALTER TABLE `recruit_interview_evaluations` CHANGE `interview_schedule_id` `recruit_interview_schedule_id` INT UNSIGNED NULL DEFAULT NULL;');
                $table->foreign('recruit_interview_schedule_id', 'riev_recruit_interview_schedule_id_foreign')->references('id')->on('recruit_interview_schedules')->onUpdate('cascade')->onDelete('cascade');
                $table->dropForeign('recruit_interview_evaluations_interview_schedule_id_foreign');

                DB::statement('ALTER TABLE `recruit_interview_evaluations` CHANGE `stage_id` `recruit_interview_stage_id` INT UNSIGNED NULL DEFAULT NULL;');
                $table->foreign('recruit_interview_stage_id', 'rie_recruit_interview_stage_id_foreign')->references('id')->on('recruit_interview_stages')->onUpdate('cascade')->onDelete('cascade');
                $table->dropForeign('recruit_interview_evaluations_stage_id_foreign');

                DB::statement('ALTER TABLE `recruit_interview_evaluations` CHANGE `job_application_id` `recruit_job_application_id` INT UNSIGNED NULL DEFAULT NULL;');
                $table->foreign('recruit_job_application_id', 'rie_recruit_job_application_id_foreign')->references('id')->on('recruit_job_applications')->onUpdate('cascade')->onDelete('cascade');
                $table->dropForeign('recruit_interview_evaluations_job_application_id_foreign');
            });
        }

        if (! Schema::hasColumn('recruit_job_addresses', 'company_address_id')) {
            Schema::table('recruit_job_addresses', function (Blueprint $table) {
                DB::statement('ALTER TABLE `recruit_job_addresses` CHANGE `job_id` `recruit_job_id` BIGINT UNSIGNED NOT NULL;');
                $table->foreign('recruit_job_id')->references('id')->on('recruit_jobs')
                    ->onUpdate('cascade')->onDelete('cascade');
                $table->dropForeign('recruit_job_addresses_job_id_foreign');

                DB::statement('ALTER TABLE `recruit_job_addresses` CHANGE `address_id` `company_address_id` BIGINT UNSIGNED NOT NULL;');
                $table->foreign('company_address_id')->references('id')->on('company_addresses')
                    ->onUpdate('cascade')->onDelete('cascade');
                $table->dropForeign('recruit_job_addresses_address_id_foreign');
            });
        }

        if (! Schema::hasColumn('offer_letter_histories', 'recruit_job_offer_file_id')) {
            Schema::table('offer_letter_histories', function (Blueprint $table) {
                DB::statement('ALTER TABLE `offer_letter_histories` CHANGE `job_offer_id` `recruit_job_offer_letter_id` INT UNSIGNED NULL DEFAULT NULL;');
                $table->foreign('recruit_job_offer_letter_id')->references('id')->on('recruit_job_offer_letter')
                    ->onUpdate('cascade')->onDelete('cascade');
                $table->dropForeign('offer_letter_histories_job_offer_id_foreign');

                DB::statement('ALTER TABLE `offer_letter_histories` CHANGE `file_id` `recruit_job_offer_file_id` INT UNSIGNED NULL DEFAULT NULL;');
                $table->foreign('recruit_job_offer_file_id')->references('id')->on('recruit_job_offer_files')
                    ->onUpdate('cascade')->onDelete('cascade');
                $table->dropForeign('offer_letter_histories_file_id_foreign');
            });
        }

        if (! Schema::hasColumn('job_interview_stages', 'recruit_interview_stage_id')) {
            Schema::table('job_interview_stages', function (Blueprint $table) {
                DB::statement('ALTER TABLE `job_interview_stages` CHANGE `job_id` `recruit_job_id` BIGINT UNSIGNED NOT NULL;');
                $table->foreign('recruit_job_id')->references('id')->on('recruit_jobs')
                    ->onUpdate('cascade')->onDelete('cascade');
                $table->dropForeign('job_interview_stages_job_id_foreign');

                DB::statement('ALTER TABLE `job_interview_stages` CHANGE `stage_id` `recruit_interview_stage_id` INT UNSIGNED NOT NULL;');
                $table->foreign('recruit_interview_stage_id', 'jis_recruit_interview_stage_id_foreign')->references('id')->on('recruit_interview_stages')->onUpdate('cascade')->onDelete('cascade');
                $table->dropForeign('job_interview_stages_stage_id_foreign');
            });

            DB::statement('ALTER TABLE `recruit_candidate_database` CHANGE `job_id` `recruit_job_id` BIGINT UNSIGNED NOT NULL;');
        }

        $recruitModule = \App\Models\Module::firstOrCreate(['module_name' => 'recruit']);

        $customPermissions = [
            'manage_job_category',
            'manage_job_sub_category',
        ];

        foreach ($customPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'display_name' => ucwords(str_replace('_', ' ', $permission)),
                'is_custom' => 1,
                'module_id' => $recruitModule->id,
                'allowed_permissions' => Permission::ALL_NONE,
            ]);

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
