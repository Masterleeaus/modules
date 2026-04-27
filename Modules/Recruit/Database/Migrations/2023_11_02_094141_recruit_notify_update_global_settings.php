<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('recruit_global_settings') || Schema::hasColumn('recruit_global_settings', 'notify_update')) {
            return;
        }

        $afterColumn = Schema::hasColumn('recruit_global_settings', 'supported_until') ? 'supported_until' : null;

        Schema::table('recruit_global_settings', function (Blueprint $table) use ($afterColumn) {
            $column = $table->boolean('notify_update')->default(1);
            if ($afterColumn) {
                $column->after($afterColumn);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('recruit_global_settings') || ! Schema::hasColumn('recruit_global_settings', 'notify_update')) {
            return;
        }

        Schema::table('recruit_global_settings', function (Blueprint $table) {
            $table->dropColumn('notify_update');
        });
    }
};
