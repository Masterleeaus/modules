<?php

use App\Scopes\CompanyScope;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Recruit\Entities\ApplicationSource;
use Modules\Recruit\Entities\RecruitGlobalSetting;
use Modules\Recruit\Entities\RecruitSetting;

return new class extends Migration
{
    public function up()
    {
        Schema::create('recruit_global_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->string('purchase_code')->nullable();
            $table->string('license_type', 20)->nullable();
            $table->timestamp('supported_until')->nullable();
            $table->timestamps();
        });

        $setting = RecruitSetting::withoutGlobalScope(CompanyScope::class)->first();

        $newSetting = new RecruitGlobalSetting;

        if ($setting) {
            $newSetting->purchase_code = $setting->purchase_code;
        }
        $newSetting->saveQuietly();

        if (Schema::hasTable('recruit_settings')) {


        Schema::table('recruit_settings', function (Blueprint $table) {
            $table->dropColumn(['purchase_code']);
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
        if (Schema::hasTable('zoom_setting')) {

        Schema::table('zoom_setting', function (Blueprint $table) {
            $table->dropColumn(['purchase_code']);
        });

        }
    }

};
