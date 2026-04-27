<?php

use App\Models\Company;
use App\Models\ModuleSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Asset\Entities\AssetSetting;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return array
     */
    public function up()
    {
        \App\Models\Module::validateVersion(AssetSetting::MODULE_NAME);

        \App\Models\Module::where('module_name', 'assets')->update([
            'module_name' => AssetSetting::MODULE_NAME,
        ]);

        ModuleSetting::where('module_name', 'assets')->update([
            'module_name' => AssetSetting::MODULE_NAME,
        ]);

        $tables = ['asset_types', 'assets', 'asset_lending_history'];

        $count = Company::count();

        try {

            foreach ($tables as $table) {

                if (! Schema::hasTable($table)) {
                    continue;
                }

                if (! Schema::hasColumn($table, 'company_id')) {
                    Schema::table($table, function (Blueprint $table) {
                        $table->integer('company_id')->unsigned()->nullable()->after('id');
                        $table->foreign('company_id')->references('id')
                            ->on('companies')->onDelete('cascade')->onUpdate('cascade');
                    });
                }

                if (Schema::hasColumn($table, 'company_id') && $count === 1) {
                    DB::table($table)->update(['company_id' => 1]);
                }
            }

        } catch (Exception $e) {
            echo $e->getMessage();
        }

        $companies = Company::all();

        // We will insert these for the new company from event listener also
        foreach ($companies as $company) {
            AssetSetting::addModuleSetting($company);
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
