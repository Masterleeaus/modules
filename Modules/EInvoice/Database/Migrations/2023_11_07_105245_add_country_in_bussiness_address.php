<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('company_addresses') || Schema::hasColumn('company_addresses', 'country_id')) {
            return;
        }

        $afterColumn = Schema::hasColumn('company_addresses', 'company_id') ? 'company_id' : null;

        Schema::table('company_addresses', function (Blueprint $table) use ($afterColumn) {
            $column = $table->unsignedInteger('country_id')->nullable();
            if ($afterColumn) {
                $column->after($afterColumn);
            }
        });

        if (Schema::hasTable('countries')) {
            try {
                Schema::table('company_addresses', function (Blueprint $table) {
                    $table->foreign('country_id')->references('id')->on('countries')->nullOnDelete();
                });
            } catch (\Throwable $exception) {
                logger($exception->getMessage());
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('company_addresses') || ! Schema::hasColumn('company_addresses', 'country_id')) {
            return;
        }

        try {
            Schema::table('company_addresses', function (Blueprint $table) {
                $table->dropForeign(['country_id']);
            });
        } catch (\Throwable $exception) {
            logger($exception->getMessage());
        }

        Schema::table('company_addresses', function (Blueprint $table) {
            $table->dropColumn('country_id');
        });
    }
};
