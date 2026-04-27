<?php

use App\Models\Company;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Recruit\Entities\RecruitSetting;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (Schema::hasTable('recruit_settings')) {
            if (! Schema::hasColumn('recruit_settings', 'form_settings')) {
                Schema::table('recruit_settings', function (Blueprint $table) {
                    $table->longText('form_settings')->after('mail_setting')->nullable();
                });

                $companies = Company::all();

                foreach ($companies as $company) {
                    RecruitSetting::recruitSettingInsert($company);
                }
            }
        }

        if (Schema::hasTable('recruit_job_applications')) {
            DB::statement('ALTER TABLE `recruit_job_applications` CHANGE `phone` `phone` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL;');
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
