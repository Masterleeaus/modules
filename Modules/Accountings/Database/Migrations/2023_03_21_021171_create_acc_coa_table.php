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
        if (!Schema::hasTable('acc_coa')) {
            Schema::create('acc_coa', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('bs_id')->nullable();
            $table->foreign('bs_id')->references('id')->on('acc_map_bs')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('pnl_id')->nullable();
            $table->foreign('pnl_id')->references('id')->on('acc_map_pnl')->onDelete('cascade')->onUpdate('cascade');
            $table->string('coa');
            $table->string('coa_desc');
            $table->boolean('detail')->default(false);
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
        Schema::dropIfExists('acc_coa');
    }
};
