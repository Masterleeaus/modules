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
        if (! Schema::hasColumn('assets', 'image')) {
            if (Schema::hasTable('assets')) {
        Schema::table('assets', function (Blueprint $table) {
                if (!Schema::hasColumn('assets', 'image')) {
                    $table->string('image')->nullable()->after('serial_number');
                }
        });
    }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('assets')) {
        Schema::table('assets', function (Blueprint $table) {
            if (Schema::hasColumn('assets', 'image')) {
                $table->dropColumn('image');
            }
        });
    }
    }
};
