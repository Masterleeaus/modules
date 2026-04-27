<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('inspection_templates')) {
            return;
        }

        Schema::table('inspection_templates', function (Blueprint $table) {
            if (!Schema::hasColumn('inspection_templates', 'service_type_id')) {
                $table->unsignedBigInteger('service_type_id')->nullable()->after('company_id');
            }

            if (!Schema::hasColumn('inspection_templates', 'items_json')) {
                $table->json('items_json')->nullable()->after('description');
            }

            if (!Schema::hasColumn('inspection_templates', 'weight_config')) {
                $table->json('weight_config')->nullable()->after('items_json');
            }

            if (!Schema::hasColumn('inspection_templates', 'pass_threshold')) {
                $table->unsignedTinyInteger('pass_threshold')->default(70)->after('weight_config');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('inspection_templates')) {
            return;
        }

        Schema::table('inspection_templates', function (Blueprint $table) {
            $cols = ['service_type_id', 'items_json', 'weight_config', 'pass_threshold'];

            foreach ($cols as $col) {
                if (Schema::hasColumn('inspection_templates', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
