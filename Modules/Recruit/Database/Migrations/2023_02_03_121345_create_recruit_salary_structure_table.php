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
        $recruitModule = \App\Models\Module::firstOrCreate(['module_name' => 'recruit']);

        Permission::firstOrCreate([
            'name' => 'view_dashboard',
            'display_name' => ucwords(str_replace('_', ' ', 'view_dashboard')),
            'is_custom' => 1,
            'module_id' => $recruitModule->id,
            'allowed_permissions' => Permission::ALL_NONE,
        ]);

        if (! Schema::hasColumn('recruit_job_offer_letter', 'add_structure')) {
            Schema::table('recruit_job_offer_letter', function (Blueprint $table) {
                $table->integer('add_structure')->default(0)->after('sign_require');
            });
        }

        DB::statement('ALTER TABLE `recruit_job_offer_letter` CHANGE `comp_amount` `comp_amount` DOUBLE NULL');

        if (! Schema::hasTable('recruit_salary_structures')) {
            Schema::create('recruit_salary_structures', function (Blueprint $table) {
                $table->increments('id');

                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');

                $table->integer('recruit_job_application_id')->unsigned()->nullable();
                $table->foreign('recruit_job_application_id')->references('id')->on('recruit_job_applications')->onDelete('cascade')->onUpdate('cascade');

                $table->integer('recruit_job_offer_letter_id')->unsigned()->nullable();
                $table->foreign('recruit_job_offer_letter_id')->references('id')->on('recruit_job_offer_letter')->onUpdate('cascade')->onDelete('cascade');

                $table->text('salary_json')->nullable();
                $table->string('annual_salary')->nullable();
                $table->string('basic_salary')->nullable();
                $table->enum('basic_value_type', ['fixed', 'ctc_percent'])->default(null)->nullable();
                $table->string('amount')->default('0');
                $table->double('fixed_allowance')->default(0);
                $table->date('date')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('recruit_selected_salary_components')) {
            Schema::create('recruit_selected_salary_components', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('rss_id')->unsigned()->nullable();
                $table->foreign('rss_id')->references('id')->on('recruit_salary_structures')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('salary_component_id')->unsigned()->nullable();
                $table->string('component_name')->nullable();
                $table->enum('component_type', ['earning', 'deduction'])->nullable();
                $table->string('component_value')->nullable();
                $table->enum('value_type', ['fixed', 'percent', 'basic_percent', 'variable'])->default('variable');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('recruit_candidate_follow_ups')) {
            Schema::create('recruit_candidate_follow_ups', function (Blueprint $table) {
                $table->increments('id');
            $table->unsignedBigInteger('company_id')->nullable()->index();
                $table->integer('recruit_job_application_id')->unsigned()->nullable();
                $table->foreign('recruit_job_application_id')->references('id')->on('recruit_job_applications')->onDelete('cascade')->onUpdate('cascade');

                $table->longText('remark')->nullable();
                $table->dateTime('next_follow_up_date')->nullable();
                $table->enum('send_reminder', ['yes', 'no'])->nullable()->default('no');
                $table->text('remind_time')->nullable();
                $table->enum('remind_type', ['minute', 'hour', 'day'])->nullable();
                $table->string('status')->default('incomplete')->nullable();

                $table->integer('added_by')->unsigned()->nullable();
                $table->foreign('added_by')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('cascade');

                $table->integer('last_updated_by')->unsigned()->nullable();
                $table->foreign('last_updated_by')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('cascade');
                $table->timestamps();
            });
        }

        if (! Schema::hasColumn('recruit_settings', 'google_recaptcha_status')) {
            Schema::table('recruit_settings', function (Blueprint $table) {
                $table->enum('google_recaptcha_status', ['active', 'deactive'])->default('deactive');
            });
        }

        if (! Schema::hasColumn('recruit_job_applications', 'expected_ctc_rate')) {
            Schema::table('recruit_job_applications', function (Blueprint $table) {
                $table->string('currenct_ctc_rate')->after('current_ctc')->nullable();
            });
        }

        if (! Schema::hasColumn('recruit_job_applications', 'expected_ctc_rate')) {
            Schema::table('recruit_job_applications', function (Blueprint $table) {
                $table->string('expected_ctc_rate')->after('expected_ctc')->nullable();
            });
        }

        if (Schema::hasColumn('recruit_jobs', 'location_id')) {
            Schema::table('recruit_jobs', function (Blueprint $table) {

                $foreignKeys = $this->listTableForeignKeys('recruit_jobs');

                if (in_array('recruit_jobs_location_id_foreign', $foreignKeys)) {
                    $table->dropForeign('recruit_jobs_location_id_foreign');
                }

                $table->dropColumn('location_id');
            });
        }

        if (! Schema::hasColumn('recruit_job_applications', 'rejection_remark')) {
            Schema::table('recruit_job_applications', function (Blueprint $table) {
                $table->string('rejection_remark')->after('remark')->nullable();
            });
        }

        DB::statement("ALTER TABLE recruit_job_applications MODIFY COLUMN total_experience ENUM('fresher','0-1', '1-2','2-3','3-4','4-5', '5-6','6-7', '7-8', '8-9', '9-10', '10-11', '11-12', '12-13', '13-14', 'over-15')");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recruit_salary_structure');
        Schema::dropIfExists('recruit_selected_salary_components');
        Schema::dropIfExists('recruit_candidate_follow_ups');
    }

    public function listTableForeignKeys($table)
    {
        $conn = Schema::getConnection()->getDoctrineSchemaManager();

        return array_map(function ($key) {
            return $key->getName();
        }, $conn->listTableForeignKeys($table));
    }
};
