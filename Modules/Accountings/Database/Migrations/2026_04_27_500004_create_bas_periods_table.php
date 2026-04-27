<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('bas_periods')) {
            Schema::create('bas_periods', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('organization_id')->index();
                $table->string('period_type', 20)->default('quarterly')->comment('quarterly|monthly');
                $table->date('period_start')->index();
                $table->date('period_end')->index();
                $table->decimal('gst_collected', 15, 2)->default(0);
                $table->decimal('gst_paid', 15, 2)->default(0);
                $table->decimal('net_gst', 15, 2)->default(0);
                $table->string('status', 20)->default('draft')->comment('draft|lodged');
                $table->timestamp('lodged_at')->nullable();
                $table->timestamp('locked_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bas_periods');
    }
};
