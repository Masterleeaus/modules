<?php

use App\Models\ModuleSetting;
use App\Models\Permission;
use App\Models\PermissionType;
use App\Models\RoleUser;
use App\Models\UserPermission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        \App\Models\Module::validateVersion(RecruitSetting::MODULE_NAME);

        Schema::create('recruit_skills', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('recruit_job_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->string('job_type');
            $table->timestamps();
        });

        Schema::create('recruit_work_experiences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->string('work_experience');
            $table->timestamps();
        });

        Schema::create('recruit_jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->text('title');
            $table->string('slug')->nullable();
            $table->unsignedBigInteger('job_type_id')->nullable();
            $table->foreign('job_type_id')->references('id')->on('recruit_job_types')->onUpdate('cascade')->onDelete('cascade');
            $table->longText('job_description')->nullable();
            $table->integer('total_positions');
            $table->integer('remaining_openings');

            $table->unsignedInteger('department_id')->nullable();
            $table->foreign('department_id')->references('id')->on('teams')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('location_id')->nullable();
            $table->foreign('location_id')->references('id')->on('company_addresses')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('recruiter_id');
            $table->enum('job_type', ['part time', 'full time'])->default('full time');

            $table->dateTime('start_date');
            $table->dateTime('end_date');

            $table->enum('status', ['open', 'closed'])->default('open');
            $table->text('meta_details');

            $table->boolean('is_photo_require')->default(false);
            $table->boolean('is_resume_require')->default(false);
            $table->boolean('is_dob_require')->default(false);
            $table->boolean('is_gender_require')->default(false);

            $table->unsignedBigInteger('work_experience_id')->nullable();
            $table->foreign('work_experience_id')->references('id')->on('recruit_work_experiences')->onUpdate('cascade')->onDelete('cascade');

            $table->string('pay_type');
            $table->double('start_amount');
            $table->double('end_amount')->nullable();
            $table->enum('pay_according', ['hour', 'day', 'week', 'month', 'year']);

            $table->integer('added_by')->unsigned()->nullable();
            $table->foreign('added_by')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('cascade');

            $table->integer('last_updated_by')->unsigned()->nullable();
            $table->foreign('last_updated_by')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('recruit_interview_stages', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('recruit_job_skills', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->bigInteger('job_id')->unsigned();
            $table->foreign('job_id')->references('id')->on('recruit_jobs')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('skill_id')->unsigned();
            $table->foreign('skill_id')->references('id')->on('recruit_skills')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });

        Schema::create('recruit_application_status_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('application_sources', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->string('application_source');
            $table->timestamps();
        });

        Schema::create('recruit_application_status', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->string('status');
            $table->string('slug');
            $table->string('color');
            $table->integer('position');
            $table->bigInteger('category_id')->unsigned()->nullable()->default(null);
            $table->foreign('category_id')->references('id')->on('recruit_application_status_categories')->onUpdate('cascade')->onDelete('cascade');
            $table->enum('action', ['yes', 'no'])->default('no');
            $table->timestamps();
        });

        Schema::create('recruit_job_files', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('job_id')->unsigned();
            $table->foreign('job_id')->references('id')->on('recruit_jobs')->onDelete('cascade')->onUpdate('cascade');
            $table->string('filename');
            $table->text('description')->nullable();
            $table->string('google_url')->nullable();
            $table->string('hashname')->nullable();
            $table->string('size')->nullable();
            $table->string('dropbox_link')->nullable();
            $table->string('external_link')->nullable();
            $table->string('external_link_name')->nullable();
            $table->integer('added_by')->unsigned()->nullable();
            $table->foreign('added_by')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('cascade');

            $table->integer('last_updated_by')->unsigned()->nullable();
            $table->foreign('last_updated_by')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('cascade');

            $table->timestamps();
        });

        Schema::create('recruit_job_applications', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->string('photo')->nullable();
            $table->text('resume');
            $table->integer('column_priority')->nullable();
            $table->string('remark');
            $table->mediumText('cover_letter')->nullable();
            $table->enum('job_type', ['part time', 'full time', 'internship'])->default('full time')->nullable();

            $table->bigInteger('job_id')->unsigned();
            $table->foreign('job_id')->references('id')->on('recruit_jobs')->onUpdate('cascade')->onDelete('cascade');

            $table->integer('status_id')->unsigned()->nullable()->default(null);
            $table->foreign('status_id')->references('id')->on('recruit_application_status')->onUpdate('cascade')->onDelete('cascade');

            $table->bigInteger('location_id')->unsigned();
            $table->foreign('location_id')->references('id')->on('company_addresses')->onUpdate('cascade')->onDelete('cascade');

            $table->enum('application_sources', ['careerWebsite', 'addedByUser']);

            $table->integer('source_id')->unsigned()->nullable();
            $table->foreign('source_id')->references('id')->on('application_sources')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('file_id')->unsigned()->nullable();
            $table->foreign('file_id')->references('id')->on('recruit_job_files')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('added_by')->unsigned()->nullable();
            $table->foreign('added_by')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('cascade');

            $table->integer('last_updated_by')->unsigned()->nullable();
            $table->foreign('last_updated_by')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('cascade');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('recruit_applicant_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->integer('job_application_id')->unsigned();
            $table->foreign('job_application_id')->references('id')->on('recruit_job_applications')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->text('note_text');
            $table->timestamps();
        });

        Schema::create('recruit_application_skills', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->integer('application_id')->unsigned();
            $table->foreign('application_id')->references('id')->on('recruit_job_applications')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('skill_id')->unsigned();
            $table->foreign('skill_id')->references('id')->on('recruit_skills')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });

        Schema::create('recruit_jobboard_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable()->index();

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('board_column_id')->unsigned();
            $table->foreign('board_column_id')->references('id')->on('recruit_application_status')->onDelete('cascade')->onUpdate('cascade');

            $table->boolean('collapsed')->default(0);

            $table->timestamps();
        });

        Schema::create('recruiters', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('company_id')->nullable()->index();

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

            $table->enum('status', ['enabled', 'disabled'])->default('enabled');

            $table->integer('added_by')->unsigned();
            $table->foreign('added_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');

            $table->integer('last_updated_by')->unsigned()->nullable();
            $table->foreign('last_updated_by')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('cascade');

            $table->timestamps();
        });

        Schema::create('recruit_interview_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->integer('job_application_id')->unsigned();
            $table->foreign('job_application_id')->references('id')->on('recruit_job_applications')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->enum('interview_type', ['in person', 'video', 'phone'])->default('in person');
            $table->dateTime('schedule_date')->nullable();
            $table->enum('status', ['rejected', 'hired', 'pending', 'canceled', 'completed'])->default('pending');
            $table->enum('user_accept_status', ['accept', 'refuse', 'waiting'])->default('waiting');
            $table->integer('meeting_id')->nullable();
            $table->enum('video_type', ['zoom', 'other'])->default('other')->nullable();
            $table->enum('remind_type_all', ['day', 'hour', 'minute']);
            $table->boolean('notify_c')->default(0);
            $table->integer('remind_time_all')->nullable();
            $table->boolean('send_reminder_all')->default(0);
            $table->string('phone')->nullable();
            $table->string('other_link')->nullable();
            $table->string('remarks')->nullable();

            $table->integer('stage_id')->unsigned()->nullable();
            $table->foreign('stage_id')->references('id')->on('recruit_interview_stages')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('parent_id')->unsigned()->nullable();
            $table->foreign('parent_id')->references('id')->on('recruit_interview_schedules')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('added_by')->unsigned()->nullable();
            $table->foreign('added_by')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('cascade');

            $table->integer('last_updated_by')->unsigned()->nullable();
            $table->foreign('last_updated_by')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('cascade');
            $table->timestamps();
        });

        Schema::create('recruit_interview_employees', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->integer('interview_schedule_id')->unsigned();

            $table->foreign('interview_schedule_id')->references('id')->on('recruit_interview_schedules')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->integer('user_id')->unsigned();

            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->enum('user_accept_status', ['accept', 'refuse', 'waiting'])->default('waiting');
            $table->timestamps();
        });

        Schema::create('recruit_interview_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->integer('interview_schedule_id')->unsigned();

            $table->foreign('interview_schedule_id')->references('id')->on('recruit_interview_schedules')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->integer('user_id')->unsigned();

            $table->foreign('user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->text('comment')->nullable()->default(null);
            $table->timestamps();
        });

        Schema::create('recruit_application_files', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->integer('application_id')->unsigned();
            $table->foreign('application_id')->references('id')->on('recruit_job_applications')->onDelete('cascade')->onUpdate('cascade');

            $table->string('filename');
            $table->string('hashname');
            $table->string('size');
            $table->text('description')->nullable();

            $table->integer('added_by')->unsigned()->nullable();
            $table->foreign('added_by')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('cascade');

            $table->integer('last_updated_by')->unsigned()->nullable();
            $table->foreign('last_updated_by')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('cascade');
            $table->timestamps();
        });

        Schema::create('recruit_candidate_database', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->string('name');
            $table->bigInteger('job_id')->unsigned();
            $table->bigInteger('location_id')->unsigned();
            $table->date('Job_applied_on');
            $table->longText('skills');
            $table->bigInteger('job_application_id')->unsigned();
            $table->timestamps();
        });

        ModuleSetting::where(['module_name' => 'jobApplication'])->delete();

        Schema::create('recruit_footer_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->string('title');
            $table->string('slug');
            $table->longText('description')->nullable();
            $table->string('status');
            $table->timestamps();
        });

        Schema::create('recruit_job_offer_letter', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('company_id')->nullable()->index();

            $table->integer('job_app_id')->unsigned()->nullable()->default(null);
            $table->foreign('job_app_id')->references('id')->on('recruit_job_applications')->onUpdate('cascade')->onDelete('cascade');

            $table->bigInteger('job_id')->unsigned()->nullable()->default(null);
            $table->foreign('job_id')->references('id')->on('recruit_jobs')->onUpdate('cascade')->onDelete('cascade');

            $table->integer('employee_id')->unsigned()->nullable()->default(null);

            $table->date('job_expire');
            $table->date('expected_joining_date');
            $table->double('comp_amount');
            $table->string('status');
            $table->enum('pay_according', ['hour', 'day', 'week', 'month', 'year']);
            $table->string('sign_require')->nullable();
            $table->string('sign_image')->nullable();
            $table->string('decline_reason')->nullable();
            $table->string('hash')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamp('offer_accept_at')->nullable();

            $table->integer('added_by')->unsigned()->nullable();
            $table->foreign('added_by')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('cascade');

            $table->integer('last_updated_by')->unsigned()->nullable();
            $table->foreign('last_updated_by')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('cascade');

            $table->timestamps();
        });

        Schema::create('recruit_job_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id')->nullable()->index();

            $table->bigInteger('job_id')->unsigned()->nullable();
            $table->foreign('job_id')->references('id')->on('recruit_jobs')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('job_application_id')->unsigned()->nullable();
            $table->foreign('job_application_id')->references('id')->on('recruit_job_applications')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('offer_id')->unsigned()->nullable();
            $table->foreign('offer_id')->references('id')->on('recruit_job_offer_letter')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('interview_schedule_id')->unsigned()->nullable();
            $table->foreign('interview_schedule_id')->references('id')->on('recruit_interview_schedules')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

            $table->text('details');

            $table->timestamps();
        });

        Schema::create('recruit_interview_files', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('interview_id')->unsigned();
            $table->foreign('interview_id')->references('id')->on('recruit_interview_schedules')->onDelete('cascade')->onUpdate('cascade');
            $table->string('filename');
            $table->text('description')->nullable();
            $table->string('google_url')->nullable();
            $table->string('hashname')->nullable();
            $table->string('size')->nullable();
            $table->string('dropbox_link')->nullable();
            $table->string('external_link')->nullable();
            $table->string('external_link_name')->nullable();

            $table->integer('added_by')->unsigned()->nullable();
            $table->foreign('added_by')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('cascade');

            $table->integer('last_updated_by')->unsigned()->nullable();
            $table->foreign('last_updated_by')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('cascade');

            $table->timestamps();
        });

        Schema::create('recruit_interview_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable()->index();

            $table->integer('interview_schedule_id')->unsigned()->nullable();
            $table->foreign('interview_schedule_id')->references('id')->on('recruit_interview_schedules')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('file_id')->unsigned()->nullable();
            $table->foreign('file_id')->references('id')->on('recruit_interview_files')->onDelete('cascade')->onUpdate('cascade');

            $table->text('details');

            $table->timestamps();
        });

        Schema::create('recruit_recommendation_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->string('status');

            $table->integer('added_by')->unsigned()->nullable();
            $table->foreign('added_by')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('cascade');

            $table->integer('last_updated_by')->unsigned()->nullable();
            $table->foreign('last_updated_by')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('cascade');

            $table->timestamps();
        });

        Schema::create('recruit_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('company_id')->nullable();
            $table->longText('about')->nullable()->default(null);
            $table->string('type')->nullable()->default('bg-image');
            $table->string('background_image')->nullable();
            $table->string('background_color')->nullable();
            $table->longText('mail_setting');
            $table->longText('legal_term')->nullable()->default(null);
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('company_name')->nullable();
            $table->string('company_website')->nullable();
            $table->string('purchase_code')->nullable()->default(null);
            $table->timestamps();
        });

        Schema::create('recruit_job_offer_files', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('company_id')->nullable()->index();

            $table->integer('job_offer_id')->unsigned();
            $table->foreign('job_offer_id')->references('id')->on('recruit_job_offer_letter')->onDelete('cascade')->onUpdate('cascade');

            $table->string('filename');
            $table->string('hashname')->nullable();

            $table->integer('added_by')->unsigned()->nullable();
            $table->foreign('added_by')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('cascade');

            $table->integer('last_updated_by')->unsigned()->nullable();
            $table->foreign('last_updated_by')->references('id')->on('users')->onDelete('SET NULL')->onUpdate('cascade');

            $table->timestamps();
        });

        Schema::create('recruit_interview_evaluations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable()->index();

            $table->integer('status_id')->unsigned()->nullable();
            $table->foreign('status_id')->references('id')->on('recruit_recommendation_statuses')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('interview_schedule_id')->unsigned()->nullable();
            $table->foreign('interview_schedule_id')->references('id')->on('recruit_interview_schedules')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('stage_id')->unsigned()->nullable();
            $table->foreign('stage_id')->references('id')->on('recruit_interview_stages')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('job_application_id')->unsigned()->nullable();
            $table->foreign('job_application_id')->references('id')->on('recruit_job_applications')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedInteger('submitted_by')->unsigned()->nullable()->default(null);
            $table->foreign('submitted_by')->references('id')->on('users')->onDelete(null)->onUpdate('cascade');

            $table->text('details');

            $table->timestamps();
        });

        Schema::create('recruit_job_addresses', function (Blueprint $table) {            $table->unsignedBigInteger('company_id')->nullable()->index();

            $table->unsignedBigInteger('job_id');
            $table->unsignedBigInteger('address_id');
            $table->foreign('job_id')->references('id')->on('recruit_jobs')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('address_id')->references('id')->on('company_addresses')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });

        Schema::create('offer_letter_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->integer('job_offer_id')->unsigned()->nullable();
            $table->foreign('job_offer_id')->references('id')->on('recruit_job_offer_letter')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('file_id')->unsigned()->nullable();
            $table->foreign('file_id')->references('id')->on('recruit_job_offer_files')->onDelete('cascade')->onUpdate('cascade');

            $table->text('details');
            $table->timestamps();
        });

        Schema::create('recruit_email_notification_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->string('setting_name');
            $table->enum('send_email', ['yes', 'no'])->default('no');
            $table->string('slug')->nullable();
            $table->timestamps();
        });

        Schema::create('job_interview_stages', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->bigInteger('job_id')->unsigned();
            $table->foreign('job_id')->references('id')->on('recruit_jobs')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('stage_id')->unsigned();
            $table->foreign('stage_id')->references('id')->on('recruit_interview_stages')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });

        $recruitModule = \App\Models\Module::firstOrCreate(['module_name' => 'recruit']);
        $id = $recruitModule->id;

        $exists = Permission::where('name', 'add_job')->exists();

        if (! $exists) {

            Permission::insert([
                ['name' => 'manage_skill', 'display_name' => 'Manage Skills', 'is_custom' => 1, 'module_id' => $id, 'allowed_permissions' => Permission::ALL_NONE],
                ['name' => 'add_job', 'display_name' => 'Add Job', 'is_custom' => 1, 'module_id' => $id, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],
                ['name' => 'view_job', 'display_name' => 'View Job', 'is_custom' => 1, 'module_id' => $id, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],
                ['name' => 'edit_job', 'display_name' => 'Edit Job', 'is_custom' => 1, 'module_id' => $id, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],
                ['name' => 'delete_job', 'display_name' => 'Delete Job', 'is_custom' => 1, 'module_id' => $id, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],
                ['name' => 'add_job_application', 'display_name' => 'Add Job Application', 'is_custom' => 1, 'module_id' => $id, 'allowed_permissions' => Permission::ALL_4_ADDED_1_NONE_5],
                ['name' => 'view_job_application', 'display_name' => 'View Job Application', 'is_custom' => 1, 'module_id' => $id, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],
                ['name' => 'edit_job_application', 'display_name' => 'Edit Job Application', 'is_custom' => 1, 'module_id' => $id, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],
                ['name' => 'delete_job_application', 'display_name' => 'Delete Job Application', 'is_custom' => 1, 'module_id' => $id, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],
                ['name' => 'add_notes', 'display_name' => 'Add Notes', 'is_custom' => 1, 'module_id' => $id, 'allowed_permissions' => Permission::ALL_4_ADDED_1_NONE_5],
                ['name' => 'edit_notes', 'display_name' => 'Edit Notes', 'is_custom' => 1, 'module_id' => $id, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],
                ['name' => 'delete_notes', 'display_name' => 'Delete Notes', 'is_custom' => 1, 'module_id' => $id, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],

                ['name' => 'add_application_status', 'display_name' => 'Add Application Status', 'is_custom' => 1, 'module_id' => $id, 'allowed_permissions' => Permission::ALL_NONE],
                ['name' => 'edit_application_status', 'display_name' => 'Edit Application Status', 'is_custom' => 1, 'module_id' => $id, 'allowed_permissions' => Permission::ALL_NONE],
                ['name' => 'delete_application_status', 'display_name' => 'Delete Application Status', 'is_custom' => 1, 'module_id' => $id, 'allowed_permissions' => Permission::ALL_NONE],
                ['name' => 'change_application_status', 'display_name' => 'Change Application Status', 'is_custom' => 1, 'module_id' => $id, 'allowed_permissions' => Permission::ALL_NONE],

                ['name' => 'add_interview_schedule', 'display_name' => 'Add Interview Schedule', 'is_custom' => 1, 'module_id' => $id, 'allowed_permissions' => Permission::ALL_4_ADDED_1_NONE_5],
                ['name' => 'view_interview_schedule', 'display_name' => 'View Interview Schedule', 'is_custom' => 1, 'module_id' => $id, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],
                ['name' => 'edit_interview_schedule', 'display_name' => 'Edit Interview Schedule', 'is_custom' => 1, 'module_id' => $id, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],
                ['name' => 'delete_interview_schedule', 'display_name' => 'Delete Interview Schedule', 'is_custom' => 1, 'module_id' => $id, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],
                ['name' => 'reschedule_interview', 'display_name' => 'Reschedule Interview', 'is_custom' => 1, 'module_id' => $id, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],

                ['name' => 'add_recommendation_status', 'display_name' => 'Add Recommendation Status', 'is_custom' => 1, 'module_id' => $id, 'allowed_permissions' => Permission::ALL_NONE],
                ['name' => 'edit_recommendation_status', 'display_name' => 'Edit Recommendation Status', 'is_custom' => 1, 'module_id' => $id, 'allowed_permissions' => Permission::ALL_NONE],
                ['name' => 'delete_recommendation_status', 'display_name' => 'Delete Recommendation Status', 'is_custom' => 1, 'module_id' => $id, 'allowed_permissions' => Permission::ALL_NONE],

                ['name' => 'add_recruiter', 'display_name' => 'Add Recruiter', 'is_custom' => 1, 'module_id' => $id, 'allowed_permissions' => Permission::ALL_NONE],
                ['name' => 'edit_recruiter', 'display_name' => 'Edit Recruiter', 'is_custom' => 1, 'module_id' => $id, 'allowed_permissions' => Permission::ALL_NONE],
                ['name' => 'delete_recruiter', 'display_name' => 'Delete Recruiter', 'is_custom' => 1, 'module_id' => $id, 'allowed_permissions' => Permission::ALL_NONE],
                ['name' => 'add_offer_letter', 'display_name' => 'Add Offer Letter', 'is_custom' => 1, 'module_id' => $id, 'allowed_permissions' => Permission::ALL_4_ADDED_1_NONE_5],
                ['name' => 'view_offer_letter', 'display_name' => 'View Offer Letter', 'is_custom' => 1, 'module_id' => $id, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],
                ['name' => 'edit_offer_letter', 'display_name' => 'Edit Offer Letter', 'is_custom' => 1, 'module_id' => $id, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],
                ['name' => 'delete_offer_letter', 'display_name' => 'Delete Offer Letter', 'is_custom' => 1, 'module_id' => $id, 'allowed_permissions' => Permission::ALL_4_ADDED_1_OWNED_2_BOTH_3_NONE_5],

                ['name' => 'add_footer_link', 'display_name' => 'Add Footer Link', 'is_custom' => 1, 'module_id' => $id, 'allowed_permissions' => Permission::ALL_NONE],
                ['name' => 'edit_footer_link', 'display_name' => 'Edit Footer Link', 'is_custom' => 1, 'module_id' => $id, 'allowed_permissions' => Permission::ALL_NONE],
                ['name' => 'delete_footer_link', 'display_name' => 'Delete Footer Link', 'is_custom' => 1, 'module_id' => $id, 'allowed_permissions' => Permission::ALL_NONE],
                ['name' => 'view_report', 'display_name' => 'View Report', 'is_custom' => 1, 'module_id' => $id, 'allowed_permissions' => Permission::ALL_NONE],
                ['name' => 'recruit_settings', 'display_name' => 'Recruit Settings', 'is_custom' => 1, 'module_id' => $id, 'allowed_permissions' => Permission::ALL_NONE],
            ]);
        }

        $allPermissions = Permission::where('module_id', $id)->get();

        $allTypePermission = PermissionType::ofType('all')->first();

        $admins = RoleUser::join('roles', 'roles.id', '=', 'role_user.role_id')
            ->where('name', 'admin')
            ->get();

        $data = [];

        foreach ($admins as $item) {
            foreach ($allPermissions as $permission) {
                $data[] = [
                    'user_id' => $item->user_id,
                    'permission_id' => $permission->id,
                    'permission_type_id' => $allTypePermission->id,
                ];
            }
        }

        UserPermission::insert($data);

    }
};
