<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('journal_entries')) {
            Schema::create('journal_entries', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('organization_id')->index();
                $table->string('reference_type', 100)->nullable()->comment('e.g. Invoice, Payment, PurchaseOrder');
                $table->unsignedBigInteger('reference_id')->nullable()->index();
                $table->text('description')->nullable();
                $table->date('entry_date')->index();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
