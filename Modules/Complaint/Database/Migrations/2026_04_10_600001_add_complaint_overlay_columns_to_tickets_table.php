<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds complaint-management columns to the core tickets table.
     * DO NOT create a complaints table — extend tickets instead.
     */
    public function up(): void
    {
        if (!Schema::hasTable('tickets')) {
            return;
        }

        Schema::table('tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('tickets', 'ticket_category')) {
                $table->string('ticket_category')->default('support')->after('status');
                // 'support' | 'complaint' | 'feedback' | 'warranty'
            }

            if (!Schema::hasColumn('tickets', 'booking_id')) {
                $table->unsignedBigInteger('booking_id')->nullable()->after('ticket_category');
            }

            if (!Schema::hasColumn('tickets', 'service_date')) {
                $table->string('service_date')->nullable()->after('booking_id');
            }

            if (!Schema::hasColumn('tickets', 'resolution_type')) {
                $table->enum('resolution_type', ['refund', 'reclean', 'credit', 'apology', 'no_action'])
                    ->nullable()
                    ->after('service_date');
            }

            if (!Schema::hasColumn('tickets', 'refund_amount')) {
                $table->decimal('refund_amount', 10, 2)->nullable()->after('resolution_type');
            }

            if (!Schema::hasColumn('tickets', 'assigned_cleaner_id')) {
                $table->unsignedBigInteger('assigned_cleaner_id')->nullable()->after('refund_amount');
            }

            if (!Schema::hasColumn('tickets', 'complaint_source')) {
                $table->string('complaint_source')->default('client')->after('assigned_cleaner_id');
                // 'client' | 'internal' | 'review_site'
            }

            if (!Schema::hasColumn('tickets', 'requires_investigation')) {
                $table->boolean('requires_investigation')->default(false)->after('complaint_source');
            }

            if (!Schema::hasColumn('tickets', 'resolved_at')) {
                $table->timestamp('resolved_at')->nullable()->after('requires_investigation');
            }

            if (!Schema::hasColumn('tickets', 'resolved_by')) {
                $table->unsignedBigInteger('resolved_by')->nullable()->after('resolved_at');
            }
        });

        // Add FK for resolved_by → users.id (deferred; guard against missing table)
        if (Schema::hasTable('users') && Schema::hasColumn('tickets', 'resolved_by')) {
            try {
                Schema::table('tickets', function (Blueprint $table) {
                    $table->foreign('resolved_by')
                        ->references('id')
                        ->on('users')
                        ->onDelete('set null')
                        ->onUpdate('cascade');
                });
            } catch (\Exception $e) {
                // FK may already exist on retries — safe to ignore
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('tickets')) {
            return;
        }

        // Drop FK first if it exists
        try {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dropForeign(['resolved_by']);
            });
        } catch (\Exception $e) {
            // Ignore if FK doesn't exist
        }

        $columns = [
            'resolved_by',
            'resolved_at',
            'requires_investigation',
            'complaint_source',
            'assigned_cleaner_id',
            'refund_amount',
            'resolution_type',
            'service_date',
            'booking_id',
            'ticket_category',
        ];

        Schema::table('tickets', function (Blueprint $table) use ($columns) {
            foreach ($columns as $col) {
                if (Schema::hasColumn('tickets', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
