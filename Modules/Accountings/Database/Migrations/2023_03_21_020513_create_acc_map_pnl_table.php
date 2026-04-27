<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('acc_map_pnl')) {
            Schema::create('acc_map_pnl', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('seq');
            $table->string('pnl_name');
            $table->enum('pnl_type', ['income', 'expense', 'other-income', 'other-expense']);
            $table->enum('pnl_group', ['revenue', 'operational-expense', 'other-income', 'other-expense']);
            $table->timestamps();
        });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acc_map_pnl');
    }
};
