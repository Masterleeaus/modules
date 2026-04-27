<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── New tables ─────────────────────────────────────────────────────

        if (! Schema::hasTable('onboarding_flows')) {
            Schema::create('onboarding_flows', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('company_id')->index();
                $table->string('name');
                $table->string('type', 20)->index(); // staff / client
                $table->string('job_type', 50)->nullable()->index();
                $table->boolean('is_active')->default(true)->index();
                $table->unsignedInteger('sort_order')->default(0);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('onboarding_flow_steps')) {
            Schema::create('onboarding_flow_steps', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('flow_id')->index();
                $table->string('step_type', 40)->index(); // policy_accept / form / checklist / video / booking_wizard
                $table->string('title');
                $table->text('description')->nullable();
                $table->text('content')->nullable();
                $table->boolean('is_required')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();

                $table->foreign('flow_id')->references('id')->on('onboarding_flows')->cascadeOnDelete();
            });
        }

        if (! Schema::hasTable('onboarding_flow_completions')) {
            Schema::create('onboarding_flow_completions', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('flow_id')->index();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('step_id')->nullable()->index(); // null = whole flow done
                $table->timestamp('completed_at');
                $table->timestamps();

                $table->index(['flow_id', 'user_id']);
            });
        }

        if (! Schema::hasTable('onboarding_nps_triggers')) {
            Schema::create('onboarding_nps_triggers', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('company_id')->index();
                $table->string('trigger_event', 50)->default('job_completed');
                $table->unsignedInteger('delay_hours')->default(2);
                $table->unsignedBigInteger('survey_id')->index();
                $table->boolean('is_active')->default(true)->index();
                $table->timestamps();

                $table->foreign('survey_id')->references('id')->on('surveys')->cascadeOnDelete();
            });
        }

        // ── Extend surveys table ────────────────────────────────────────────

        Schema::table('surveys', function (Blueprint $table) {
            if (! Schema::hasColumn('surveys', 'job_type')) {
                $table->string('job_type', 50)->nullable()->after('active');
            }

            if (! Schema::hasColumn('surveys', 'trigger_event')) {
                $table->string('trigger_event', 50)->nullable()->after('job_type');
            }

            if (! Schema::hasColumn('surveys', 'delay_hours')) {
                $table->unsignedInteger('delay_hours')->nullable()->after('trigger_event');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onboarding_nps_triggers');
        Schema::dropIfExists('onboarding_flow_completions');
        Schema::dropIfExists('onboarding_flow_steps');
        Schema::dropIfExists('onboarding_flows');

        Schema::table('surveys', function (Blueprint $table) {
            foreach (['job_type', 'trigger_event', 'delay_hours'] as $col) {
                if (Schema::hasColumn('surveys', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
