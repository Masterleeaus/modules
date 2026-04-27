<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('asset_warranties')) {
            return;
        }

        Schema::create('asset_warranties', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('asset_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('additional_cost', 22, 4)->default(0);
            $table->text('additional_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('asset_warranties');
    }
};
