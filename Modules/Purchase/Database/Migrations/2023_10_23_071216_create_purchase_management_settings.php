<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Purchase\Entities\PurchaseManagementSetting;

return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        if (!Schema::hasTable('purchase_management_settings')) {
            Schema::create('purchase_management_settings', function (Blueprint $table) {
                $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id')->nullable()->index();
                $table->string('purchase_code')->nullable();
                $table->timestamp('supported_until')->nullable();
                $table->timestamps();
            });

            PurchaseManagementSetting::create([]);
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_management_settings');
    }

};
