<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('survey_user')) {
            return;
        }
        Schema::create('survey_user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id')->index();
            $table->unsignedBigInteger('survey_id')->index();
            $table->json('responses')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedSmallInteger('step')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'survey_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('survey_id')->references('id')->on('surveys')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_user');
    }
};
