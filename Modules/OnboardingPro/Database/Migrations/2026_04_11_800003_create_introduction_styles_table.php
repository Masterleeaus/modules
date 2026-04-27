<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('introduction_styles')) {
            return;
        }
        Schema::create('introduction_styles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('style', ['modal', 'banner', 'wizard', 'tooltip'])->default('modal');
            $table->enum('position', ['top', 'bottom'])->nullable();
            $table->boolean('active')->default(false)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('introduction_styles');
    }
};
