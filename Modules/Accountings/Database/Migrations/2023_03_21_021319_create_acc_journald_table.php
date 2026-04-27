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
        if (!Schema::hasTable('acc_journald')) {
            Schema::create('acc_journald', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('journal_id')->nullable();
            $table->foreign('journal_id')->references('id')->on('acc_journalh')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('coa_id')->nullable();
            $table->foreign('coa_id')->references('id')->on('acc_coa')->onDelete('cascade')->onUpdate('cascade');
            $table->double('debit');
            $table->double('credit');
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
        Schema::dropIfExists('acc_journald');
    }
};
