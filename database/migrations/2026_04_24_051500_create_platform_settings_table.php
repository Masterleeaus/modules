<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // This migration was originally a duplicate of 2026_04_24_000001_create_platform_settings_table.php.
        // It has been converted to an additive alter migration to prevent "Table already exists" errors.
        Schema::table('platform_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('platform_settings', 'app_name')) {
                $table->string('app_name')->default('FieldOps Hub');
            }
            if (! Schema::hasColumn('platform_settings', 'logo_path')) {
                $table->string('logo_path')->nullable();
            }
            if (! Schema::hasColumn('platform_settings', 'favicon_path')) {
                $table->string('favicon_path')->nullable();
            }
            if (! Schema::hasColumn('platform_settings', 'primary_color')) {
                $table->string('primary_color', 20)->default('#2563eb');
            }
            if (! Schema::hasColumn('platform_settings', 'support_email')) {
                $table->string('support_email')->nullable();
            }
            if (! Schema::hasColumn('platform_settings', 'footer_text')) {
                $table->string('footer_text')->nullable();
            }
        });
    }

    public function down(): void
    {
        // Non-destructive rollback: columns are owned by the canonical create migration.
    }
};
