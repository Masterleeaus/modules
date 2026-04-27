<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('inspection_schedule_files') && !Schema::hasColumn('inspection_schedule_files', 'company_id')) {
            Schema::table('inspection_schedule_files', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('inspection_schedule_items') && !Schema::hasColumn('inspection_schedule_items', 'company_id')) {
            Schema::table('inspection_schedule_items', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('inspection_schedule_recurring') && !Schema::hasColumn('inspection_schedule_recurring', 'company_id')) {
            Schema::table('inspection_schedule_recurring', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('inspection_schedule_recurring_items') && !Schema::hasColumn('inspection_schedule_recurring_items', 'company_id')) {
            Schema::table('inspection_schedule_recurring_items', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('inspection_schedule_replies') && !Schema::hasColumn('inspection_schedule_replies', 'company_id')) {
            Schema::table('inspection_schedule_replies', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('inspection_schedules') && !Schema::hasColumn('inspection_schedules', 'company_id')) {
            Schema::table('inspection_schedules', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('inspection_template_items') && !Schema::hasColumn('inspection_template_items', 'company_id')) {
            Schema::table('inspection_template_items', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
        if (Schema::hasTable('inspection_templates') && !Schema::hasColumn('inspection_templates', 'company_id')) {
            Schema::table('inspection_templates', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        // intentionally non-destructive
    }
};
