<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A: Recurring Job Templates
        Schema::create('recurring_job_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('property_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('job_type_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('frequency', 50); // weekly, biweekly, monthly, custom
            $table->string('recurrence_rule', 255)->nullable(); // RRULE string for custom
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('last_generated_on')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'is_active']);
            $table->index(['organization_id', 'start_date']);
        });

        // Add recurrence_rule to field_jobs for one-off overrides
        Schema::table('field_jobs', function (Blueprint $table) {
            $table->unsignedBigInteger('recurring_template_id')->nullable()->after('estimate_id');
            $table->foreign('recurring_template_id')->references('id')->on('recurring_job_templates')->nullOnDelete();
        });

        // B: Room/Area Checklist Templates — add section column
        Schema::table('job_type_checklist_items', function (Blueprint $table) {
            $table->string('section', 100)->nullable()->after('label');
        });

        Schema::table('job_checklist_items', function (Blueprint $table) {
            $table->string('section', 100)->nullable()->after('label');
        });

        // C: Cleaning Packages & Frequency-Based Pricing
        Schema::table('estimate_packages', function (Blueprint $table) {
            $table->string('frequency', 50)->nullable()->after('is_recommended'); // weekly, biweekly, monthly
            $table->decimal('frequency_discount', 5, 2)->nullable()->after('frequency'); // % discount e.g. 10.00
        });

        // D: Before/After Photo Documentation
        Schema::table('attachments', function (Blueprint $table) {
            $table->string('attachment_type', 50)->nullable()->after('tag'); // before_photo, after_photo, supply_usage, other
        });

        // E: Automated Client Reminders
        Schema::table('field_jobs', function (Blueprint $table) {
            $table->timestamp('reminder_sent_24h_at')->nullable()->after('cancelled_at');
            $table->timestamp('reminder_sent_2h_at')->nullable()->after('reminder_sent_24h_at');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->string('reminder_preference', 20)->default('email')->after('notes'); // email, sms, both, none
        });

        // F: Client Portal Tokens (magic links)
        Schema::create('client_portal_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('token', 64)->unique();
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->index(['token', 'expires_at']);
        });

        // G: Supply / Inventory Tracking
        Schema::create('job_supply_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('field_jobs')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity_used', 10, 3)->default(1);
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('job_id');
        });

        // H: Tipping & Rating
        Schema::create('job_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('field_jobs')->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedTinyInteger('rating'); // 1-5
            $table->text('comment')->nullable();
            $table->decimal('tip_amount', 10, 2)->default(0);
            $table->timestamps();

            $table->index('job_id');
            $table->index('technician_id');
        });

        // I: Crew / Multi-Technician Jobs
        Schema::create('job_crew', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('field_jobs')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role', 30)->default('support'); // lead, support
            $table->timestamps();

            $table->unique(['job_id', 'user_id']);
            $table->index('job_id');
        });
    }

    public function down(): void
    {
        Schema::table('job_crew', fn ($t) => null);
        Schema::dropIfExists('job_crew');
        Schema::dropIfExists('job_reviews');
        Schema::dropIfExists('job_supply_usages');
        Schema::dropIfExists('client_portal_tokens');

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('reminder_preference');
        });
        Schema::table('field_jobs', function (Blueprint $table) {
            $table->dropColumn(['reminder_sent_24h_at', 'reminder_sent_2h_at', 'recurring_template_id']);
        });
        Schema::table('attachments', function (Blueprint $table) {
            $table->dropColumn('attachment_type');
        });
        Schema::table('estimate_packages', function (Blueprint $table) {
            $table->dropColumn(['frequency', 'frequency_discount']);
        });
        Schema::table('job_checklist_items', function (Blueprint $table) {
            $table->dropColumn('section');
        });
        Schema::table('job_type_checklist_items', function (Blueprint $table) {
            $table->dropColumn('section');
        });

        Schema::dropIfExists('recurring_job_templates');
    }
};
