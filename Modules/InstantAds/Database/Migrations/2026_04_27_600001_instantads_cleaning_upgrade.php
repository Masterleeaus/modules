<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Extend ai_image_pro ────────────────────────────────────────────

        Schema::table('ai_image_pro', function (Blueprint $table) {
            if (! Schema::hasColumn('ai_image_pro', 'template_key')) {
                $table->string('template_key', 100)->nullable()->after('completed_at');
            }

            if (! Schema::hasColumn('ai_image_pro', 'brand_kit_id')) {
                $table->unsignedBigInteger('brand_kit_id')->nullable()->after('template_key');
            }

            if (! Schema::hasColumn('ai_image_pro', 'social_format')) {
                $table->string('social_format', 30)->nullable()->after('brand_kit_id');
            }

            if (! Schema::hasColumn('ai_image_pro', 'ad_copy')) {
                $table->text('ad_copy')->nullable()->after('social_format');
            }

            if (! Schema::hasColumn('ai_image_pro', 'is_published_to_library')) {
                $table->boolean('is_published_to_library')->default(false)->after('ad_copy');
            }

            if (! Schema::hasColumn('ai_image_pro', 'job_ref')) {
                $table->string('job_ref')->nullable()->after('is_published_to_library');
            }
        });

        // ── New tables ─────────────────────────────────────────────────────

        if (! Schema::hasTable('instant_ads_brand_kits')) {
            Schema::create('instant_ads_brand_kits', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('company_id')->index();
                $table->string('name');
                $table->string('primary_color', 7);
                $table->string('secondary_color', 7);
                $table->text('logo_path')->nullable();
                $table->string('tagline', 255)->nullable();
                $table->boolean('is_default')->default(false)->index();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('instant_ads_templates')) {
            Schema::create('instant_ads_templates', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('key', 100)->unique();
                $table->string('name');
                $table->string('category', 30)->index(); // seasonal / evergreen / service_type
                $table->string('job_type', 50)->nullable()->index();
                $table->text('prompt_template');
                $table->text('negative_prompt')->nullable();
                $table->boolean('is_active')->default(true)->index();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('instant_ads_templates');
        Schema::dropIfExists('instant_ads_brand_kits');

        Schema::table('ai_image_pro', function (Blueprint $table) {
            foreach (['template_key', 'brand_kit_id', 'social_format', 'ad_copy', 'is_published_to_library', 'job_ref'] as $col) {
                if (Schema::hasColumn('ai_image_pro', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
