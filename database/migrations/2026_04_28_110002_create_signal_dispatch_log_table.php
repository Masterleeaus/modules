<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signal_dispatch_log', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('signal_id')->index();
            $table->foreign('signal_id')->references('id')->on('signals')->cascadeOnDelete();
            $table->string('handler');
            $table->enum('result', ['success', 'failure', 'retry']);
            $table->unsignedSmallInteger('attempts')->default(1);
            $table->text('last_error')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signal_dispatch_log');
    }
};
