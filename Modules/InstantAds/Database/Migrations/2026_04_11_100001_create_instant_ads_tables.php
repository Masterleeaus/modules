<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ai_image_pro')) {
            Schema::create('ai_image_pro', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('user_id')->nullable()->index();
                $table->string('guest_ip')->nullable();
                $table->string('model')->nullable();
                $table->string('engine')->nullable();
                $table->text('prompt');
                $table->json('params')->nullable();
                $table->string('status')->default('pending');
                $table->json('generated_images')->nullable();
                $table->unsignedInteger('image_width')->nullable();
                $table->unsignedInteger('image_height')->nullable();
                $table->json('metadata')->nullable();
                $table->boolean('published')->default(false);
                $table->unsignedBigInteger('likes_count')->default(0);
                $table->unsignedBigInteger('views_count')->default(0);
                $table->string('share_token', 32)->nullable()->index();
                $table->timestamp('publish_requested_at')->nullable();
                $table->timestamp('publish_reviewed_at')->nullable();
                $table->unsignedInteger('publish_reviewed_by')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'status']);
                $table->index('created_at');
            });
        }

        if (! Schema::hasTable('ai_image_pro_likes')) {
            Schema::create('ai_image_pro_likes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('ai_image_pro_id')->index();
                $table->unsignedInteger('user_id')->nullable()->index();
                $table->string('guest_ip')->nullable();
                $table->timestamps();

                $table->unique(['ai_image_pro_id', 'user_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_image_pro_likes');
        Schema::dropIfExists('ai_image_pro');
    }
};
