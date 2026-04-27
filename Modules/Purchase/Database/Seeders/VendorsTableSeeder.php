<?php

namespace Modules\Purchase\Database\Seeders;

use App\Models\Company;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Modules\Purchase\Entities\PurchaseVendor;

class VendorsTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run($companyId)
    {
        $faker = Factory::create();
        $setting = Company::find($companyId);

        foreach (range(0, 20) as $item) {
            $vendor = [
                'primary_name' => $faker->name,
                'company_name' => $faker->name,
                'email' => $faker->email(),
                'phone' => $faker->phoneNumber(),
                'currency_id' => $setting->currency_id,
                'opening_balance' => 0,
                'billing_address' => fake()->address(),
                'shipping_address' => fake()->address(),
                'company_id' => $companyId,
            ];

            PurchaseVendor::create($vendor);
        }
    }

}
