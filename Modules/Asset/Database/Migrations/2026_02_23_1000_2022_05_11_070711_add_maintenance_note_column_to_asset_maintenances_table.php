<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('asset_maintenances')) {
            return;
        }
        Schema::table('asset_maintenances', function (Blueprint $table) {
            if (!Schema::hasColumn('asset_maintenances', 'maintenance_note')) {
            $table->text('maintenance_note')->nullable()->after('details');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasTable('asset_maintenances')) {
            return;
        }
    }
};
