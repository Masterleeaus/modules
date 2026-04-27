<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Biometric\Entities\BiometricGlobalSetting;

return new class extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        \App\Models\Module::validateVersion(BiometricGlobalSetting::MODULE_NAME);

        if (!Schema::hasTable('biometric_global_settings')) {
            Schema::create('biometric_global_settings', function (Blueprint $table) {
                $table->id();
            $table->unsignedBigInteger('company_id')->nullable()->index();
                $table->string('purchase_code')->nullable();
                $table->timestamp('supported_until')->nullable();
                $table->timestamp('purchased_on')->nullable();
                $table->boolean('notify_update')->default(1);
                $table->string('license_type', 20)->nullable();
                $table->timestamps();
            });

            BiometricGlobalSetting::create([]);
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biometric_global_settings');
    }

};
