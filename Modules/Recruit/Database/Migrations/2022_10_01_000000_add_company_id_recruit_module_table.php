<?php

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Recruit\Entities\RecruitApplicationStatus;
use Modules\Recruit\Entities\RecruitJobboardSetting;
use Modules\Recruit\Entities\RecruitSetting;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return array
     */
    public function up()
    {
        if (!Schema::hasColumn('application_sources', 'is_predefined')) {
            Schema::table('application_sources', function (Blueprint $table) {
                $table->boolean('is_predefined')->default(true);
                $table->integer('company_id')->unsigned()->after('id')->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
            });
        }


        \App\Models\Module::validateVersion(RecruitSetting::MODULE_NAME);

        $tables = [
            'recruiters',
            'recruit_email_notification_settings',
            'recruit_skills',
            'recruit_jobs',
            'recruit_interview_stages',
            'recruit_application_status_categories',
            'recruit_application_status',
            'recruit_application_status',
            'recruit_job_applications',
            'recruit_footer_links',
            'recruit_job_types',
            'recruit_work_experiences',
            'recruit_interview_schedules',
            'recruit_candidate_database',
            'recruit_job_offer_letter',
            'recruit_interview_evaluations',
            'recruit_recommendation_statuses',
            'application_sources'
        ];

        $count = Company::count();



        try {

            foreach ($tables as $table) {

                if (! Schema::hasColumn($table, 'company_id')) {
                    Schema::table($table, function (Blueprint $table) {
                        $table->integer('company_id')->unsigned()->nullable()->after('id');
                        $table->foreign('company_id')->references('id')
                            ->on('companies')->onDelete('cascade')->onUpdate('cascade');
                    });
                }

                if (Schema::hasColumn($table, 'company_id') && $count === 1) {
                    DB::table($table)->update(['company_id' => 1]);
                }
            }

        } catch (Exception $e) {
            logger($e->getMessage());
        }

        if (Schema::hasTable('recruit_settings')) {
            if (! Schema::hasColumn('recruit_settings', 'form_settings')) {
                Schema::table('recruit_settings', function (Blueprint $table) {
                    $table->longText('form_settings')->after('mail_setting')->nullable();
                });
            }
        }
        $companies = Company::all();

        // We will insert these for the new company from event listener also
        foreach ($companies as $company) {
            RecruitSetting::addModuleSetting($company);

            $employees = User::allEmployees(null, false, null, $company->id);

            $jobBoardColumn = RecruitApplicationStatus::where('company_id', $company->id)->get();

            if (! is_null($employees) && ! is_null($jobBoardColumn)) {
                foreach ($employees as $item) {
                    foreach ($jobBoardColumn as $board) {
                        RecruitJobboardSetting::firstOrCreate([
                            'user_id' => $item->id,
                            'recruit_application_status_id' => $board->id,
                        ]);
                    }
                }
            }

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
