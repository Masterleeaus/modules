<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organization_settings', function (Blueprint $table) {
            $table->json('setup_completed_steps')->nullable()->after('logo_path');
            $table->boolean('setup_complete')->default(false)->after('setup_completed_steps');
            $table->string('brand_color', 7)->nullable()->after('setup_complete');
            $table->string('customer_facing_name')->nullable()->after('brand_color');
        });
    }

    public function down(): void
    {
        Schema::table('organization_settings', function (Blueprint $table) {
            $table->dropColumn(['setup_completed_steps', 'setup_complete', 'brand_color', 'customer_facing_name']);
        });
    }
};
