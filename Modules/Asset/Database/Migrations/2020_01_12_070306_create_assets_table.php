<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('assets')) {
            Schema::create('assets', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name', 100);

                $table->unsignedBigInteger('asset_type_id')->nullable();
                $table->foreign('asset_type_id')->references('id')->on('asset_types')->onUpdate('cascade')->onDelete('SET NULL');

                $table->string('serial_number', 255)->nullable();
                $table->index('serial_number');

                $table->text('description')->nullable();
                $table->enum('status', ['lent', 'available', 'non-functional'])->default('available');

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
        Schema::dropIfExists('assets');
    }

};
