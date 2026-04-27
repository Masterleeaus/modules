<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('qc_record_items')) {
            return;
        }

        Schema::create('qc_record_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id')->nullable()->index();

            $table->unsignedBigInteger('record_id')->index();
            $table->string('item_label', 191);
            $table->unsignedTinyInteger('score')->default(0)->comment('0-100 or 0-10 per item');
            $table->unsignedTinyInteger('weight')->default(0)->comment('Percentage weight of this item (0-100)');
            $table->text('notes')->nullable();
            $table->string('photo', 500)->nullable()->comment('Relative path to proof photo');

            $table->timestamps();

            $table->foreign('record_id', 'fk_qc_record_items_record_id')
                ->references('id')
                ->on('qc_records')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->index(['record_id', 'item_label']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qc_record_items');
    }
};
