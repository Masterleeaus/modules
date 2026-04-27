<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('qc_records')) {
            return;
        }

        Schema::create('qc_records', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id')->nullable()->index();

            // Loose coupling: nullable foreign references (no hard FK) to keep module independent.
            $table->string('booking_id', 36)->nullable()->index();
            $table->unsignedBigInteger('cleaner_id')->nullable()->index();
            $table->unsignedBigInteger('template_id')->nullable()->index();

            // Link back to the inspection_schedules if one exists.
            $table->unsignedBigInteger('schedule_id')->nullable()->index();

            $table->unsignedTinyInteger('overall_score')->default(0)->comment('0-100');

            // status: pending | pass | fail | reclean_required | reclean_done
            $table->string('status', 30)->default('pending');

            $table->boolean('reclean_triggered')->default(false);
            $table->timestamp('reclean_triggered_at')->nullable();
            $table->unsignedBigInteger('reclean_job_id')->nullable()->comment('FSMOrder / Job ID for reclean task');

            $table->unsignedBigInteger('complaint_id')->nullable()->comment('Linked complaint if QC failed');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('inspected_by')->nullable();
            $table->timestamp('inspected_at')->nullable();

            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index(['cleaner_id', 'overall_score']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qc_records');
    }
};
