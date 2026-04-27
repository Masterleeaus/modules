<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     */

    public function up(): void
    {
        try {
            if (Schema::hasColumn('application_sources', 'application_source')) {
                if (Schema::hasTable('application_sources')) {

                Schema::table('application_sources', function (Blueprint $table) {
                    $table->dropUnique('application_sources_application_source_unique');
                });

                }
            }
        } catch (\Exception $e) {

        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }

};
