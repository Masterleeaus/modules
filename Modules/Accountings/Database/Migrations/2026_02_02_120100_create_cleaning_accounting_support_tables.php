<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pass 1: minimal scaffolding tables required for cleaning accounting.
     * These are intentionally thin; Pass 2 adds workflows & UI.
     */
    public function up(): void
    {
        // Vendors (suppliers / subcontractors)
        if (!Schema::hasTable('acc_vendors')) {
            Schema::create('acc_vendors', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('company_id')->nullable()->index();
                $t->unsignedBigInteger('user_id')->nullable()->index();
                $t->string('name', 191);
                $t->string('email', 191)->nullable();
                $t->string('phone', 50)->nullable();
                $t->string('abn', 32)->nullable();
                $t->text('address')->nullable();
                $t->boolean('is_active')->default(true);
                $t->timestamps();
                $t->index(['company_id', 'user_id']);
            });
        }

        // Tax codes (AU-ready basics; rows may be global templates with user_id NULL)
        if (!Schema::hasTable('acc_tax_codes')) {
            Schema::create('acc_tax_codes', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('company_id')->nullable()->index();
                $t->unsignedBigInteger('user_id')->nullable()->index();
                $t->string('code', 20)->index(); // GST, FRE, N-T, etc.
                $t->string('name', 191);
                $t->decimal('rate', 6, 4)->default(0); // 0.1000 = 10%
                $t->boolean('is_active')->default(true);
                $t->timestamps();
                $t->index(['company_id', 'user_id']);
            });
        }

        // Service lines (Residential, Bond, Carpet, Pressure, Pool, Commercial...)
        if (!Schema::hasTable('acc_service_lines')) {
            Schema::create('acc_service_lines', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('company_id')->nullable()->index();
                $t->unsignedBigInteger('user_id')->nullable()->index();
                $t->string('name', 191);
                $t->string('slug', 191)->nullable()->index();
                $t->boolean('is_active')->default(true);
                $t->timestamps();
                $t->index(['company_id', 'user_id']);
            });
        }

        // Bills (accounts payable)
        if (!Schema::hasTable('acc_bills')) {
            Schema::create('acc_bills', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('company_id')->nullable()->index();
                $t->unsignedBigInteger('user_id')->nullable()->index();
                $t->unsignedBigInteger('vendor_id')->nullable()->index();
                $t->string('bill_number', 191)->nullable()->index();
                $t->date('bill_date')->nullable()->index();
                $t->date('due_date')->nullable()->index();
                $t->decimal('subtotal', 16, 2)->default(0);
                $t->decimal('tax_total', 16, 2)->default(0);
                $t->decimal('total', 16, 2)->default(0);
                $t->string('status', 30)->default('draft')->index(); // draft|approved|unpaid|partial|paid|void
                $t->text('notes')->nullable();
                $t->timestamps();
                $t->index(['company_id', 'user_id']);
            });
        }

        if (!Schema::hasTable('acc_bill_lines')) {
            Schema::create('acc_bill_lines', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('company_id')->nullable()->index();
                $t->unsignedBigInteger('user_id')->nullable()->index();
                $t->unsignedBigInteger('bill_id')->index();
                $t->unsignedBigInteger('coa_id')->nullable()->index(); // expense/COGS account
                $t->unsignedBigInteger('tax_code_id')->nullable()->index();
                $t->unsignedBigInteger('service_line_id')->nullable()->index();
                $t->string('description', 191)->nullable();
                $t->decimal('qty', 12, 2)->default(1);
                $t->decimal('unit_price', 16, 2)->default(0);
                $t->decimal('line_subtotal', 16, 2)->default(0);
                $t->decimal('line_tax', 16, 2)->default(0);
                $t->decimal('line_total', 16, 2)->default(0);
                $t->timestamps();
                $t->index(['company_id', 'user_id']);
            });
        }

        // Receipts / evidence attachments (file storage handled by host app)
        if (!Schema::hasTable('acc_receipts')) {
            Schema::create('acc_receipts', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('company_id')->nullable()->index();
                $t->unsignedBigInteger('user_id')->nullable()->index();
                $t->string('attachable_type', 191)->nullable()->index();
                $t->unsignedBigInteger('attachable_id')->nullable()->index();
                $t->string('file_path', 500)->nullable();
                $t->string('file_name', 191)->nullable();
                $t->string('mime', 191)->nullable();
                $t->unsignedBigInteger('file_size')->nullable();
                $t->text('notes')->nullable();
                $t->timestamps();
                $t->index(['company_id', 'user_id']);
            });
        }

        // Job costs (ties costs to a job/visit in your ops module)
        if (!Schema::hasTable('acc_job_costs')) {
            Schema::create('acc_job_costs', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('company_id')->nullable()->index();
                $t->unsignedBigInteger('user_id')->nullable()->index();
                $t->string('job_ref', 191)->nullable()->index(); // flexible foreign key (job id/uuid)
                $t->unsignedBigInteger('source_bill_line_id')->nullable()->index();
                $t->unsignedBigInteger('source_invoice_line_id')->nullable()->index();
                $t->unsignedBigInteger('service_line_id')->nullable()->index();
                $t->decimal('amount', 16, 2)->default(0);
                $t->string('cost_type', 30)->default('expense')->index(); // cogs|expense
                $t->timestamps();
                $t->index(['company_id', 'user_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('acc_job_costs');
        Schema::dropIfExists('acc_receipts');
        Schema::dropIfExists('acc_bill_lines');
        Schema::dropIfExists('acc_bills');
        Schema::dropIfExists('acc_service_lines');
        Schema::dropIfExists('acc_tax_codes');
        Schema::dropIfExists('acc_vendors');
    }
};
