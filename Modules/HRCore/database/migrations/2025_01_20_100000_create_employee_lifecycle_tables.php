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
        // Employee Lifecycle States table
        if (! Schema::hasTable('employee_lifecycle_states')) {
            Schema::create('employee_lifecycle_states', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('state'); // onboarding, active, inactive, probation, relieved, terminated, retired, resigned, suspended
                $table->string('previous_state')->nullable();
                $table->date('effective_date');
                $table->text('reason')->nullable();
                $table->text('remarks')->nullable();
                $table->foreignId('approved_by')->nullable()->constrained('users');
                $table->timestamp('approved_at')->nullable();
                $table->foreignId('created_by')->constrained('users');
                $table->timestamps();

                $table->index(['user_id', 'state']);
                $table->index('effective_date');
            });
        }

        // Employee History table for tracking all changes
        if (! Schema::hasTable('employee_histories')) {
            Schema::create('employee_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('event_type'); // designation_change, team_transfer, salary_revision, status_change, etc.
                $table->json('old_data')->nullable();
                $table->json('new_data')->nullable();
                $table->text('reason')->nullable();
                $table->text('remarks')->nullable();
                $table->foreignId('changed_by')->constrained('users');
                $table->timestamp('effective_date')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'event_type']);
                $table->index('created_at');
            });
        }

        // Employee Onboarding Checklist
        if (! Schema::hasTable('employee_onboarding_checklists')) {
            Schema::create('employee_onboarding_checklists', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('task');
                $table->text('description')->nullable();
                $table->foreignId('assigned_to')->nullable()->constrained('users');
                $table->date('due_date')->nullable();
                $table->boolean('is_completed')->default(false);
                $table->timestamp('completed_at')->nullable();
                $table->foreignId('completed_by')->nullable()->constrained('users');
                $table->text('notes')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->index(['user_id', 'is_completed']);
            });
        }

        // Employee Offboarding Checklist
        if (! Schema::hasTable('employee_offboarding_checklists')) {
            Schema::create('employee_offboarding_checklists', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('task');
                $table->text('description')->nullable();
                $table->foreignId('assigned_to')->nullable()->constrained('users');
                $table->date('due_date')->nullable();
                $table->boolean('is_completed')->default(false);
                $table->timestamp('completed_at')->nullable();
                $table->foreignId('completed_by')->nullable()->constrained('users');
                $table->text('notes')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->index(['user_id', 'is_completed']);
            });
        }

        // Employee Promotions
        if (! Schema::hasTable('employee_promotions')) {
            Schema::create('employee_promotions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('old_designation_id')->nullable()->constrained('designations');
                $table->foreignId('new_designation_id')->constrained('designations');
                $table->decimal('old_salary', 10, 2)->nullable();
                $table->decimal('new_salary', 10, 2)->nullable();
                $table->decimal('salary_increment', 10, 2)->nullable();
                $table->decimal('increment_percentage', 5, 2)->nullable();
                $table->date('effective_date');
                $table->text('reason')->nullable();
                $table->text('remarks')->nullable();
                $table->foreignId('approved_by')->nullable()->constrained('users');
                $table->timestamp('approved_at')->nullable();
                $table->foreignId('created_by')->constrained('users');
                $table->timestamps();

                $table->index(['user_id', 'effective_date']);
            });
        }

        // Employee Transfers
        if (! Schema::hasTable('employee_transfers')) {
            Schema::create('employee_transfers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('old_team_id')->nullable()->constrained('teams');
                $table->foreignId('new_team_id')->constrained('teams');
                $table->foreignId('old_department_id')->nullable()->constrained('departments');
                $table->foreignId('new_department_id')->nullable()->constrained('departments');
                $table->foreignId('old_reporting_to_id')->nullable()->constrained('users');
                $table->foreignId('new_reporting_to_id')->nullable()->constrained('users');
                $table->string('transfer_type'); // internal, interdepartmental, location
                $table->date('effective_date');
                $table->text('reason')->nullable();
                $table->text('remarks')->nullable();
                $table->foreignId('approved_by')->nullable()->constrained('users');
                $table->timestamp('approved_at')->nullable();
                $table->foreignId('created_by')->constrained('users');
                $table->timestamps();

                $table->index(['user_id', 'effective_date']);
            });
        }

        // Employee Probation Details
        if (! Schema::hasTable('employee_probations')) {
            Schema::create('employee_probations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->date('start_date');
                $table->date('end_date');
                $table->date('extended_to')->nullable();
                $table->string('status')->default('ongoing'); // ongoing, completed, extended, failed
                $table->text('performance_notes')->nullable();
                $table->text('areas_of_improvement')->nullable();
                $table->text('final_evaluation')->nullable();
                $table->foreignId('evaluated_by')->nullable()->constrained('users');
                $table->timestamp('evaluated_at')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'status']);
            });
        }

        // Add lifecycle columns to users table if they don't exist
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'employee_status')) {
                $table->string('employee_status')->default('active')->after('status');
            }
            if (! Schema::hasColumn('users', 'probation_end_date')) {
                $table->date('probation_end_date')->nullable()->after('date_of_joining');
            }
            if (! Schema::hasColumn('users', 'resignation_date')) {
                $table->date('resignation_date')->nullable()->after('probation_end_date');
            }
            if (! Schema::hasColumn('users', 'last_working_date')) {
                $table->date('last_working_date')->nullable()->after('resignation_date');
            }
            if (! Schema::hasColumn('users', 'exit_reason')) {
                $table->text('exit_reason')->nullable()->after('last_working_date');
            }
            if (! Schema::hasColumn('users', 'exit_remarks')) {
                $table->text('exit_remarks')->nullable()->after('exit_reason');
            }

            // Add index if not exists
            if (! Schema::hasIndex('users', 'users_employee_status_index')) {
                $table->index('employee_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove lifecycle columns from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'employee_status',
                'probation_end_date',
                'resignation_date',
                'last_working_date',
                'exit_reason',
                'exit_remarks',
            ]);
        });

        // Drop tables
        Schema::dropIfExists('employee_probations');
        Schema::dropIfExists('employee_transfers');
        Schema::dropIfExists('employee_promotions');
        Schema::dropIfExists('employee_offboarding_checklists');
        Schema::dropIfExists('employee_onboarding_checklists');
        Schema::dropIfExists('employee_histories');
        Schema::dropIfExists('employee_lifecycle_states');
    }
};
