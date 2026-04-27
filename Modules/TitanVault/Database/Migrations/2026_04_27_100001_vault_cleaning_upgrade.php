<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // -----------------------------------------------------------------
        // 1. Extend vault_documents with cleaning-specific columns
        // -----------------------------------------------------------------
        Schema::table('vault_documents', function (Blueprint $table) {
            if (!Schema::hasColumn('vault_documents', 'folder')) {
                $table->string('folder')->nullable()->after('status')
                    ->comment('Jobs / Compliance / Contracts / Staff / Marketing');
            }

            if (!Schema::hasColumn('vault_documents', 'job_ref')) {
                $table->string('job_ref')->nullable()->after('folder')->index();
            }

            if (!Schema::hasColumn('vault_documents', 'document_type')) {
                $table->string('document_type')->nullable()->after('job_ref')
                    ->comment('compliance / proof_pack / contract / evidence / sds / insurance');
            }

            // expires_at already exists in the original migration as a timestamp.
            // We do NOT re-add it — guard below is left for safety.
            if (!Schema::hasColumn('vault_documents', 'expiry_alert_days')) {
                $table->unsignedInteger('expiry_alert_days')->default(30)->after('expires_at');
            }

            if (!Schema::hasColumn('vault_documents', 'expiry_notified_at')) {
                $table->dateTime('expiry_notified_at')->nullable()->after('expiry_alert_days');
            }
        });

        // -----------------------------------------------------------------
        // 2. vault_proof_packs
        // -----------------------------------------------------------------
        if (!Schema::hasTable('vault_proof_packs')) {
            Schema::create('vault_proof_packs', function (Blueprint $table) {
                $table->id();

                $table->unsignedInteger('company_id')->nullable()->index();
                $table->foreign('company_id')
                    ->references('id')->on('companies')
                    ->onDelete('set null')->onUpdate('cascade');

                $table->string('job_ref')->nullable()->index();

                $table->unsignedBigInteger('job_id')->nullable()->index();

                $table->string('title');

                $table->enum('status', ['draft', 'sent', 'approved', 'rejected'])->default('draft');

                $table->unsignedInteger('created_by')->nullable();
                $table->foreign('created_by')
                    ->references('id')->on('users')
                    ->onDelete('set null')->onUpdate('cascade');

                $table->timestamp('sent_at')->nullable();
                $table->timestamp('approved_at')->nullable();

                $table->string('approval_token')->unique()->nullable();

                $table->string('client_email')->nullable();
                $table->string('client_name')->nullable();

                $table->timestamps();
            });
        }

        // -----------------------------------------------------------------
        // 3. vault_proof_pack_documents (pivot)
        // -----------------------------------------------------------------
        if (!Schema::hasTable('vault_proof_pack_documents')) {
            Schema::create('vault_proof_pack_documents', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('proof_pack_id');
                $table->foreign('proof_pack_id')
                    ->references('id')->on('vault_proof_packs')
                    ->onDelete('cascade');

                $table->unsignedBigInteger('document_id');
                $table->foreign('document_id')
                    ->references('id')->on('vault_documents')
                    ->onDelete('cascade');

                $table->unsignedInteger('sort_order')->default(0);

                $table->timestamps();
            });
        }

        // -----------------------------------------------------------------
        // 4. vault_compliance_documents
        // -----------------------------------------------------------------
        if (!Schema::hasTable('vault_compliance_documents')) {
            Schema::create('vault_compliance_documents', function (Blueprint $table) {
                $table->id();

                $table->unsignedInteger('company_id')->nullable()->index();
                $table->foreign('company_id')
                    ->references('id')->on('companies')
                    ->onDelete('set null')->onUpdate('cascade');

                $table->unsignedBigInteger('document_id')->nullable();
                $table->foreign('document_id')
                    ->references('id')->on('vault_documents')
                    ->onDelete('set null');

                $table->string('compliance_type')
                    ->comment('insurance / police_check / wwcc / sds / other');

                $table->unsignedInteger('staff_id')->nullable();
                $table->foreign('staff_id')
                    ->references('id')->on('users')
                    ->onDelete('set null');

                $table->string('chemical_name')->nullable();

                $table->date('expiry_date')->nullable()->index();

                $table->timestamp('alert_sent_at')->nullable();

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('vault_compliance_documents');
        Schema::dropIfExists('vault_proof_pack_documents');
        Schema::dropIfExists('vault_proof_packs');

        Schema::table('vault_documents', function (Blueprint $table) {
            foreach (['folder', 'job_ref', 'document_type', 'expiry_alert_days', 'expiry_notified_at'] as $col) {
                if (Schema::hasColumn('vault_documents', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
