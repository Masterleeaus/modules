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
        if (!Schema::hasTable('acc_map_bs')) {
            Schema::create('acc_map_bs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('seq');
            $table->string('bs_name');
            $table->enum('bs_type', ['aktiva', 'passiva', 'capital']);
            $table->enum('bs_group', ['current-assets', 'fixed-assets', 'intangible-assets', 'current_liabilities', 'long-term-liabilities', 'equity' ]);
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
        Schema::dropIfExists('acc_balance_sheet');
    }
};
