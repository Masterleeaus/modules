<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('acc_recurring_expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->string('name', 191);
            $table->unsignedBigInteger('coa_id')->nullable()->index(); // optional expense account
            $table->decimal('amount', 16, 2)->default(0);
            $table->string('frequency', 20)->default('monthly'); // weekly|monthly|quarterly|yearly
            $table->unsignedTinyInteger('day_of_month')->nullable(); // for monthly
            $table->unsignedTinyInteger('day_of_week')->nullable(); // 1-7 for weekly
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acc_recurring_expenses');
    }
};
