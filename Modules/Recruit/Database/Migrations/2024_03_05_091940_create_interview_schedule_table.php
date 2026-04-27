<?php

use App\Models\Company;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Modules\Recruit\Entities\RecruitInterviewSchedule;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    public function up(): void
    {
        $companies = Company::select('id', 'timezone')->get();

        foreach($companies as $company)
        {
            $interviews = RecruitInterviewSchedule::where('company_id', $company->id)->get();

            foreach($interviews as $interview)
            {
                try {
                    // Original datetime object
                    $originalDatetime = $interview->schedule_date->shiftTimezone($company->timezone);
                    // Change timezone without changing time
                    $newDatetime = $originalDatetime->copy()->tz('UTC');

                    $interview->schedule_date = $newDatetime;
                    $interview->save();
                }catch (\Exception $e){

                }


            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('interview_schedule')) {

        Schema::table('interview_schedule', function (Blueprint $table) {

        });

        }
    }

};
