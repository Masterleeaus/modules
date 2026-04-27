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
        if(!Schema::hasTable('timesheets'))
        {
            Schema::create('timesheets', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id')->nullable();
                $table->integer('project_id')->nullable();
                $table->integer('task_id')->nullable();
                $table->date('date');
                $table->integer('hours')->default(0);
                $table->integer('minutes')->default(0);
                $table->string('type');
                $table->longText('notes')->nullable();
                $table->integer('workspace_id')->nullable();
                $table->integer('created_by');
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
        Schema::dropIfExists('timesheets');
    }
};
