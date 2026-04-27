<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // --- Upgrade projects table ---
        Schema::table('projects', function (Blueprint $table) {
            if (! Schema::hasColumn('projects', 'job_type')) {
                $table->string('job_type')->nullable()->after('type');
            }
            if (! Schema::hasColumn('projects', 'recurrence')) {
                $table->string('recurrence')->nullable()->default('none')->after('job_type');
            }
            if (! Schema::hasColumn('projects', 'recurrence_parent_id')) {
                $table->unsignedBigInteger('recurrence_parent_id')->nullable()->after('recurrence');
                $table->foreign('recurrence_parent_id')->references('id')->on('projects')->nullOnDelete();
            }
            if (! Schema::hasColumn('projects', 'address')) {
                $table->string('address')->nullable()->after('recurrence_parent_id');
            }
            if (! Schema::hasColumn('projects', 'suburb')) {
                $table->string('suburb')->nullable()->after('address');
            }
            if (! Schema::hasColumn('projects', 'state')) {
                $table->string('state')->nullable()->after('suburb');
            }
            if (! Schema::hasColumn('projects', 'postcode')) {
                $table->string('postcode', 4)->nullable()->after('state');
            }
            if (! Schema::hasColumn('projects', 'access_instructions')) {
                $table->text('access_instructions')->nullable()->after('postcode');
            }
            if (! Schema::hasColumn('projects', 'client_notes')) {
                $table->text('client_notes')->nullable()->after('access_instructions');
            }
            if (! Schema::hasColumn('projects', 'job_ref')) {
                $table->string('job_ref')->nullable()->after('client_notes');
            }
            if (! Schema::hasColumn('projects', 'quoted_price')) {
                $table->decimal('quoted_price', 10, 2)->nullable()->after('job_ref');
            }
            if (! Schema::hasColumn('projects', 'actual_price')) {
                $table->decimal('actual_price', 10, 2)->nullable()->after('quoted_price');
            }
        });

        // --- Upgrade timesheets table ---
        Schema::table('timesheets', function (Blueprint $table) {
            if (! Schema::hasColumn('timesheets', 'clock_in_at')) {
                $table->dateTime('clock_in_at')->nullable()->after('approved_at');
            }
            if (! Schema::hasColumn('timesheets', 'clock_out_at')) {
                $table->dateTime('clock_out_at')->nullable()->after('clock_in_at');
            }
            if (! Schema::hasColumn('timesheets', 'clock_in_lat')) {
                $table->decimal('clock_in_lat', 10, 7)->nullable()->after('clock_out_at');
            }
            if (! Schema::hasColumn('timesheets', 'clock_out_lat')) {
                $table->decimal('clock_out_lat', 10, 7)->nullable()->after('clock_in_lat');
            }
            if (! Schema::hasColumn('timesheets', 'clock_in_lng')) {
                $table->decimal('clock_in_lng', 10, 7)->nullable()->after('clock_out_lat');
            }
            if (! Schema::hasColumn('timesheets', 'clock_out_lng')) {
                $table->decimal('clock_out_lng', 10, 7)->nullable()->after('clock_in_lng');
            }
            if (! Schema::hasColumn('timesheets', 'travel_minutes')) {
                $table->unsignedInteger('travel_minutes')->nullable()->after('clock_out_lng');
            }
            if (! Schema::hasColumn('timesheets', 'award_rate_type')) {
                $table->string('award_rate_type')->nullable()->after('travel_minutes');
            }
            if (! Schema::hasColumn('timesheets', 'award_rate_multiplier')) {
                $table->decimal('award_rate_multiplier', 4, 2)->default(1.00)->after('award_rate_type');
            }
        });

        // --- New table: project_checklists ---
        if (! Schema::hasTable('project_checklists')) {
            Schema::create('project_checklists', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('project_id')->nullable();
                $table->string('name');
                $table->string('job_type')->nullable();
                $table->boolean('is_template')->default(false);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('project_id')->references('id')->on('projects')->nullOnDelete();
                $table->index(['project_id']);
                $table->index(['job_type', 'is_template']);
            });
        }

        // --- New table: project_checklist_items ---
        if (! Schema::hasTable('project_checklist_items')) {
            Schema::create('project_checklist_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('checklist_id');
                $table->string('description');
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_required')->default(false);
                $table->timestamps();

                $table->foreign('checklist_id')->references('id')->on('project_checklists')->cascadeOnDelete();
                $table->index(['checklist_id', 'sort_order']);
            });
        }

        // --- New table: project_checklist_completions ---
        if (! Schema::hasTable('project_checklist_completions')) {
            Schema::create('project_checklist_completions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('checklist_id');
                $table->unsignedBigInteger('item_id');
                $table->unsignedBigInteger('project_id');
                $table->unsignedBigInteger('completed_by')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();

                $table->foreign('checklist_id')->references('id')->on('project_checklists')->cascadeOnDelete();
                $table->foreign('item_id')->references('id')->on('project_checklist_items')->cascadeOnDelete();
                $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete();
                $table->unique(['item_id', 'project_id'], 'unique_item_project_completion');
                $table->index(['project_id']);
            });
        }

        // --- New table: project_materials ---
        if (! Schema::hasTable('project_materials')) {
            Schema::create('project_materials', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('project_id');
                $table->string('material_name');
                $table->decimal('quantity', 10, 3)->default(1);
                $table->string('unit')->default('unit');
                $table->decimal('unit_cost', 10, 2)->nullable();
                $table->timestamps();

                $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete();
                $table->index(['project_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('project_materials');
        Schema::dropIfExists('project_checklist_completions');
        Schema::dropIfExists('project_checklist_items');
        Schema::dropIfExists('project_checklists');

        Schema::table('timesheets', function (Blueprint $table) {
            $table->dropColumnIfExists([
                'clock_in_at', 'clock_out_at',
                'clock_in_lat', 'clock_out_lat',
                'clock_in_lng', 'clock_out_lng',
                'travel_minutes', 'award_rate_type', 'award_rate_multiplier',
            ]);
        });

        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'recurrence_parent_id')) {
                $table->dropForeign(['recurrence_parent_id']);
            }
            $table->dropColumnIfExists([
                'job_type', 'recurrence', 'recurrence_parent_id',
                'address', 'suburb', 'state', 'postcode',
                'access_instructions', 'client_notes', 'job_ref',
                'quoted_price', 'actual_price',
            ]);
        });
    }
};
