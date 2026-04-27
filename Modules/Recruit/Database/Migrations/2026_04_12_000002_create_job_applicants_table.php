<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the `job_applicants` table which represents the pre-employee
     * recruitment pipeline. Applicants in this table are NOT yet employees.
     * When status transitions to 'hired', EmployeeController@store is called
     * to create the user/employee record and `converted_employee_id` is set.
     */
    public function up(): void
    {
        if (Schema::hasTable('job_applicants')) {
            return;
        }

        Schema::create('job_applicants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('position_applied'); // 'cleaner','supervisor','area_manager','office'
            $table->string('status')->default('applied'); // 'applied','screening','interview','offer','hired','rejected'
            $table->unsignedBigInteger('job_posting_id')->nullable()->index();
            $table->text('cover_letter')->nullable();
            $table->string('resume_path')->nullable(); // stored on private disk
            $table->string('source')->nullable(); // 'website','seek','indeed','referral'
            $table->date('availability_date')->nullable();
            $table->json('interview_notes')->nullable();
            $table->unsignedInteger('interviewer_id')->nullable(); // FK → users.id (INT)
            $table->timestamp('offer_sent_at')->nullable();
            $table->timestamp('offer_accepted_at')->nullable();
            $table->unsignedInteger('converted_employee_id')->nullable(); // FK → users.id when hired (INT)
            $table->timestamps();
            $table->softDeletes();

            // Unique email per company — prevents duplicate applications
            $table->unique(['company_id', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applicants');
    }
};
