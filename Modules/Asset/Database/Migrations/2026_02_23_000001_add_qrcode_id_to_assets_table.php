<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('assets')) {
            return;
        }
        Schema::table('assets', function (Blueprint $table) {
            if (!Schema::hasColumn('assets', 'qrcode_id')) {
                $table->unsignedBigInteger('qrcode_id')->nullable()->index();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('assets')) {
            return;
        }
        Schema::table('assets', function (Blueprint $table) {
            if (Schema::hasColumn('assets', 'qrcode_id')) {
                $table->dropColumn('qrcode_id');
            }
        });
    }
};
