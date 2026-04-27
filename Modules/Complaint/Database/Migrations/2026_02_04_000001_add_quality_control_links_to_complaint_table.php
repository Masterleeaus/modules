<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('complaint')) {
            Schema::table('complaint', function (Blueprint $table) {
                if (!Schema::hasColumn('complaint', 'quality_control_id')) {
                    $table->unsignedInteger('quality_control_id')->nullable()->after('type_id');
                }
                if (!Schema::hasColumn('complaint', 'quality_control_reason')) {
                    $table->string('quality_control_reason')->nullable()->after('quality_control_id');
                }
                if (!Schema::hasColumn('complaint', 'job_id')) {
                    $table->unsignedBigInteger('job_id')->nullable()->after('quality_control_reason');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('complaint')) {
            Schema::table('complaint', function (Blueprint $table) {
                foreach (['job_id', 'quality_control_reason', 'quality_control_id'] as $col) {
                    if (Schema::hasColumn('complaint', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
