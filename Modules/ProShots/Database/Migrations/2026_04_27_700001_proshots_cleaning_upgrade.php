<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Extend pebblely table ──────────────────────────────────────────

        Schema::table('pebblely', function (Blueprint $table) {
            if (! Schema::hasColumn('pebblely', 'job_ref')) {
                $table->string('job_ref')->nullable()->after('image');
            }

            if (! Schema::hasColumn('pebblely', 'room_type')) {
                $table->string('room_type', 30)->nullable()->after('job_ref');
            }

            if (! Schema::hasColumn('pebblely', 'photo_stage')) {
                $table->string('photo_stage', 10)->nullable()->after('room_type');
            }

            if (! Schema::hasColumn('pebblely', 'is_published_to_vault')) {
                $table->boolean('is_published_to_vault')->default(false)->after('photo_stage');
            }

            if (! Schema::hasColumn('pebblely', 'vault_document_id')) {
                $table->unsignedBigInteger('vault_document_id')->nullable()->after('is_published_to_vault');
            }
        });

        // ── New tables ─────────────────────────────────────────────────────

        if (! Schema::hasTable('proshots_background_presets')) {
            Schema::create('proshots_background_presets', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('preset_key', 100)->unique();
                $table->string('category', 30)->index(); // residential / commercial / outdoor
                $table->text('description');
                $table->text('thumbnail_url')->nullable();
                $table->boolean('is_default')->default(false)->index();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('proshots_job_batches')) {
            Schema::create('proshots_job_batches', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('company_id')->index();
                $table->string('job_ref')->index();
                $table->string('title');
                $table->string('status', 20)->default('pending')->index(); // pending / processing / completed
                $table->unsignedInteger('total_photos')->default(0);
                $table->unsignedInteger('completed_photos')->default(0);
                $table->unsignedBigInteger('vault_proof_pack_id')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->index(['company_id', 'job_ref']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('proshots_job_batches');
        Schema::dropIfExists('proshots_background_presets');

        Schema::table('pebblely', function (Blueprint $table) {
            foreach (['job_ref', 'room_type', 'photo_stage', 'is_published_to_vault', 'vault_document_id'] as $col) {
                if (Schema::hasColumn('pebblely', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
