<?php

use App\Models\Company;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Recruit\Entities\ApplicationSource;
use Modules\Recruit\Entities\RecruitJobApplication;

return new class extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('application_sources', 'is_predefined')) {
            if (Schema::hasTable('application_sources')) {
        Schema::table('application_sources', function (Blueprint $table) {
                if (!Schema::hasColumn('application_sources', 'is_predefined')) {
                    $table->boolean('is_predefined')->default(true);
                }
                if (!Schema::hasColumn('application_sources', 'company_id')) {
                    $table->integer('company_id')->unsigned()->after('id')->nullable();
                }
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
        });
    }
        }

        $companies = Company::all();


        foreach ($companies as $key => $company) {
            if ($key == 0) {
                ApplicationSource::whereNull('company_id')->update(['company_id' => $company->id]);

            }
            else {
                $sourceList = [
                    ['application_source' => 'LinkedIn', 'company_id' => $company->id, 'is_predefined' => true],
                    ['application_source' => 'Facebook', 'company_id' => $company->id, 'is_predefined' => true],
                    ['application_source' => 'Instagram', 'company_id' => $company->id, 'is_predefined' => true],
                    ['application_source' => 'Twitter', 'company_id' => $company->id, 'is_predefined' => true],
                    ['application_source' => 'Other', 'company_id' => $company->id, 'is_predefined' => true],
                ];

                ApplicationSource::insertOrIgnore($sourceList);
            }

        }

        $application = RecruitJobApplication::all();

        foreach ($application as $jobApplication) {

            $source = $jobApplication->source;

            if ($source) {
                $applicationSource = ApplicationSource::where('company_id', $jobApplication->company_id)->where('application_source', $source->application_source)->first();

                if ($applicationSource) {
                    $jobApplication->application_source_id = $applicationSource->id;
                    $jobApplication->save();
                }
            }


        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('application_sources')) {
        Schema::table('application_sources', function (Blueprint $table) {
            if (Schema::hasColumn('application_sources', 'company_id')) {
                $table->dropColumn('company_id');
            }
        });
    }
    }

};
