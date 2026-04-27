<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('module_installations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('module_id');
            $table->string('version')->nullable();
            $table->enum('status', ['installing', 'installed', 'upgrading', 'failed', 'uninstalled'])->default('installing');
            $table->timestamp('installed_at')->nullable();
            $table->timestamp('last_upgraded_at')->nullable();
            $table->uuid('installed_by')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique('module_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_installations');
    }
};
