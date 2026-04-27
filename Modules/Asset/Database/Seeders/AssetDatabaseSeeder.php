<?php

namespace Modules\Asset\Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Asset\Entities\Asset;
use Modules\Asset\Entities\AssetHistory;
use Modules\Asset\Entities\AssetType;

class AssetDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        config(['app.seeding' => true]);

        if (! app()->environment('codecanyon')) {
            $companies = Company::all();

            foreach ($companies as $company) {

                \DB::table('asset_types')->insert([
                    [
                        'name' => 'Laptop',
                        'company_id' => $company->id,
                    ],
                    [
                        'name' => 'Desktop',
                        'company_id' => $company->id,
                    ],
                    [
                        'name' => 'Mobile',
                        'company_id' => $company->id,
                    ],
                    [
                        'name' => 'Printer',
                        'company_id' => $company->id,
                    ],
                    [
                        'name' => 'Scanner',
                        'company_id' => $company->id,
                    ],
                    [
                        'name' => 'Two-Wheeler',
                        'company_id' => $company->id,
                    ],
                    [
                        'name' => 'Car',
                        'company_id' => $company->id,
                    ],
                    [
                        'name' => 'Other',
                        'company_id' => $company->id,
                    ],
                ]);

                $users = User::where('company_id', $company->id)->get()->pluck('id');
                $assetTypes = AssetType::where('company_id', $company->id)->get()->pluck('id');

                $admin = User::where('company_id', $company->id)->first()->id;

                Asset::factory()->count(20)->make()->each(function (Asset $asset) use ($users, $admin, $assetTypes, $company) {
                    // Add default employee role
                    $asset->company_id = $company->id;
                    $asset->asset_type_id = $assetTypes->get(rand(0, $assetTypes->count() - 1));
                    $asset->save();

                    $assetHistories = AssetHistory::factory()->count(rand(2, 10))->make([
                        'asset_id' => $asset->id,
                    ]);

                    foreach ($assetHistories as $assetHistory) {
                        $assetHistory->user_id = $users->get(rand(0, $users->count() - 1));
                        $assetHistory->lender_id = $admin;
                        $assetHistory->returner_id = $admin;
                        $assetHistory->save();
                    }
                });
            }

        }

        config(['app.seeding' => false]);

    }
}
