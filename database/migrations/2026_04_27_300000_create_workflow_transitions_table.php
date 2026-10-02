<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_transitions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id')->nullable()->index();
            $table->string('entity_type');
            $table->string('entity_id');
            $table->string('from_state');
            $table->string('to_state');
            $table->string('transition');
            $table->unsignedBigInteger('triggered_by')->nullable();
            $table->json('context')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['entity_type', 'entity_id']);
            $table->index(['organization_id', 'entity_type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_transitions');
    }
};
