<?php

use App\Models\Company;
use App\Scopes\ActiveScope;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Biometric\Entities\BiometricSetting;

return new class extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void
    {

        if (!Schema::hasTable('biometric_settings')) {
            Schema::create('biometric_settings', function (Blueprint $table) {
                $table->id();
                $table->integer('company_id')->unsigned()->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->integer('last_transaction_id');
                $table->integer(('transaction_count'));
                $table->timestamps();
            });

            $companies = Company::withoutGlobalScopes([ActiveScope::class])->get();

            foreach ($companies as $company) {
                $bio = new BiometricSetting();
                $bio->company_id = $company->id;
                $bio->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biometric_settings');
    }
};
