<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('job_postings')) {
            return;
        }

        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->string('title');
            $table->string('position_type'); // 'cleaner','supervisor','area_manager','office'
            $table->text('description')->nullable();
            $table->text('requirements')->nullable();
            $table->string('location')->nullable();
            $table->string('employment_type')->default('casual'); // 'casual','part_time','full_time','subcontractor'
            $table->decimal('pay_rate', 10, 2)->nullable();
            $table->string('pay_unit')->default('hour'); // 'hour','day','week','month'
            $table->string('status')->default('draft'); // 'draft','published','closed'
            $table->date('close_date')->nullable();
            $table->string('slug')->nullable()->unique();
            $table->unsignedInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_postings');
    }
};
