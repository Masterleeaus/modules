<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('acc_job_costs')) {
            Schema::table('acc_job_costs', function (Blueprint $table) {
                if (! Schema::hasColumn('acc_job_costs', 'job_id')) {
                    $table->unsignedBigInteger('job_id')->nullable()->index()->after('id');
                }
                if (! Schema::hasColumn('acc_job_costs', 'service_line_id')) {
                    $table->unsignedBigInteger('service_line_id')->nullable()->index()->after('job_id');
                }
            });
        }

        if (! Schema::hasTable('acc_gst_periods')) {
            Schema::create('acc_gst_periods', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('company_id')->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('period_type', 20)->default('quarterly'); // quarterly|monthly
                $table->date('period_start');
                $table->date('period_end');
                $table->decimal('gst_collected', 15, 2)->default(0);
                $table->decimal('gst_paid', 15, 2)->default(0);
                $table->decimal('net_gst', 15, 2)->default(0);
                $table->string('status', 20)->default('draft'); // draft|lodged
                $table->timestamp('lodged_at')->nullable();
                $table->timestamp('locked_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('acc_gst_periods');

        if (Schema::hasTable('acc_job_costs')) {
            Schema::table('acc_job_costs', function (Blueprint $table) {
                if (Schema::hasColumn('acc_job_costs', 'service_line_id')) {
                    $table->dropColumn('service_line_id');
                }
                if (Schema::hasColumn('acc_job_costs', 'job_id')) {
                    $table->dropColumn('job_id');
                }
            });
        }
    }
};
