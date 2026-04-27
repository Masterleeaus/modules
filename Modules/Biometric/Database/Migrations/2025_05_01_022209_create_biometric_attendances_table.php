<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('biometric_device_attendances', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            $table->unsignedInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->string('device_name');
            $table->string('device_serial_number');
            $table->string('table');
            $table->string('stamp');
            $table->string('employee_id');
            $table->dateTime('timestamp');
            $table->boolean('status1')->nullable();
            $table->boolean('status2')->nullable();
            $table->boolean('status3')->nullable();
            $table->boolean('status4')->nullable();
            $table->boolean('status5')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
