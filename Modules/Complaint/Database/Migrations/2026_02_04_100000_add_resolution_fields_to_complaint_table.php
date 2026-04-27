<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('complaint')) {
            return;
        }

        Schema::table('complaint', function (Blueprint $table) {
            if (!Schema::hasColumn('complaint', 'sla_due_at')) {
                $table->timestamp('sla_due_at')->nullable()->after('job_id');
            }
            if (!Schema::hasColumn('complaint', 'resolved_at')) {
                $table->timestamp('resolved_at')->nullable()->after('sla_due_at');
            }
            if (!Schema::hasColumn('complaint', 'resolution_outcome')) {
                $table->string('resolution_outcome')->nullable()->after('resolved_at');
            }
            if (!Schema::hasColumn('complaint', 'follow_up_schedule_id')) {
                $table->unsignedInteger('follow_up_schedule_id')->nullable()->after('resolution_outcome');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('complaint')) {
            return;
        }

        Schema::table('complaint', function (Blueprint $table) {
            foreach (['follow_up_schedule_id', 'resolution_outcome', 'resolved_at', 'sla_due_at'] as $col) {
                if (Schema::hasColumn('complaint', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
