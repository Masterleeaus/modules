<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('application_sources') && !Schema::hasColumn('application_sources', 'company_id')) {
            Schema::table('application_sources', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('job_interview_stages') && !Schema::hasColumn('job_interview_stages', 'company_id')) {
            Schema::table('job_interview_stages', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('offer_letter_histories') && !Schema::hasColumn('offer_letter_histories', 'company_id')) {
            Schema::table('offer_letter_histories', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_applicant_notes') && !Schema::hasColumn('recruit_applicant_notes', 'company_id')) {
            Schema::table('recruit_applicant_notes', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_application_files') && !Schema::hasColumn('recruit_application_files', 'company_id')) {
            Schema::table('recruit_application_files', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_application_skills') && !Schema::hasColumn('recruit_application_skills', 'company_id')) {
            Schema::table('recruit_application_skills', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_application_status') && !Schema::hasColumn('recruit_application_status', 'company_id')) {
            Schema::table('recruit_application_status', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_application_status_categories') && !Schema::hasColumn('recruit_application_status_categories', 'company_id')) {
            Schema::table('recruit_application_status_categories', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_candidate_database') && !Schema::hasColumn('recruit_candidate_database', 'company_id')) {
            Schema::table('recruit_candidate_database', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_candidate_follow_ups') && !Schema::hasColumn('recruit_candidate_follow_ups', 'company_id')) {
            Schema::table('recruit_candidate_follow_ups', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_custom_questions') && !Schema::hasColumn('recruit_custom_questions', 'company_id')) {
            Schema::table('recruit_custom_questions', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_email_notification_settings') && !Schema::hasColumn('recruit_email_notification_settings', 'company_id')) {
            Schema::table('recruit_email_notification_settings', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_footer_links') && !Schema::hasColumn('recruit_footer_links', 'company_id')) {
            Schema::table('recruit_footer_links', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_global_settings') && !Schema::hasColumn('recruit_global_settings', 'company_id')) {
            Schema::table('recruit_global_settings', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_interview_comments') && !Schema::hasColumn('recruit_interview_comments', 'company_id')) {
            Schema::table('recruit_interview_comments', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_interview_employees') && !Schema::hasColumn('recruit_interview_employees', 'company_id')) {
            Schema::table('recruit_interview_employees', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_interview_evaluations') && !Schema::hasColumn('recruit_interview_evaluations', 'company_id')) {
            Schema::table('recruit_interview_evaluations', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_interview_files') && !Schema::hasColumn('recruit_interview_files', 'company_id')) {
            Schema::table('recruit_interview_files', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_interview_histories') && !Schema::hasColumn('recruit_interview_histories', 'company_id')) {
            Schema::table('recruit_interview_histories', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_interview_schedules') && !Schema::hasColumn('recruit_interview_schedules', 'company_id')) {
            Schema::table('recruit_interview_schedules', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_interview_stages') && !Schema::hasColumn('recruit_interview_stages', 'company_id')) {
            Schema::table('recruit_interview_stages', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_job_addresses') && !Schema::hasColumn('recruit_job_addresses', 'company_id')) {
            Schema::table('recruit_job_addresses', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_job_alerts') && !Schema::hasColumn('recruit_job_alerts', 'company_id')) {
            Schema::table('recruit_job_alerts', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_job_applications') && !Schema::hasColumn('recruit_job_applications', 'company_id')) {
            Schema::table('recruit_job_applications', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_job_categories') && !Schema::hasColumn('recruit_job_categories', 'company_id')) {
            Schema::table('recruit_job_categories', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_job_custom_answers') && !Schema::hasColumn('recruit_job_custom_answers', 'company_id')) {
            Schema::table('recruit_job_custom_answers', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_job_files') && !Schema::hasColumn('recruit_job_files', 'company_id')) {
            Schema::table('recruit_job_files', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_job_histories') && !Schema::hasColumn('recruit_job_histories', 'company_id')) {
            Schema::table('recruit_job_histories', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_job_offer_files') && !Schema::hasColumn('recruit_job_offer_files', 'company_id')) {
            Schema::table('recruit_job_offer_files', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_job_offer_letter') && !Schema::hasColumn('recruit_job_offer_letter', 'company_id')) {
            Schema::table('recruit_job_offer_letter', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_job_offer_questions') && !Schema::hasColumn('recruit_job_offer_questions', 'company_id')) {
            Schema::table('recruit_job_offer_questions', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_job_questions') && !Schema::hasColumn('recruit_job_questions', 'company_id')) {
            Schema::table('recruit_job_questions', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_job_skills') && !Schema::hasColumn('recruit_job_skills', 'company_id')) {
            Schema::table('recruit_job_skills', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_job_sub_categories') && !Schema::hasColumn('recruit_job_sub_categories', 'company_id')) {
            Schema::table('recruit_job_sub_categories', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_job_types') && !Schema::hasColumn('recruit_job_types', 'company_id')) {
            Schema::table('recruit_job_types', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_jobboard_settings') && !Schema::hasColumn('recruit_jobboard_settings', 'company_id')) {
            Schema::table('recruit_jobboard_settings', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_jobs') && !Schema::hasColumn('recruit_jobs', 'company_id')) {
            Schema::table('recruit_jobs', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_recommendation_statuses') && !Schema::hasColumn('recruit_recommendation_statuses', 'company_id')) {
            Schema::table('recruit_recommendation_statuses', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_salary_structures') && !Schema::hasColumn('recruit_salary_structures', 'company_id')) {
            Schema::table('recruit_salary_structures', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_selected_salary_components') && !Schema::hasColumn('recruit_selected_salary_components', 'company_id')) {
            Schema::table('recruit_selected_salary_components', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_settings') && !Schema::hasColumn('recruit_settings', 'company_id')) {
            Schema::table('recruit_settings', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_skills') && !Schema::hasColumn('recruit_skills', 'company_id')) {
            Schema::table('recruit_skills', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruit_work_experiences') && !Schema::hasColumn('recruit_work_experiences', 'company_id')) {
            Schema::table('recruit_work_experiences', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('recruiters') && !Schema::hasColumn('recruiters', 'company_id')) {
            Schema::table('recruiters', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        // intentionally non-destructive
    }
};
