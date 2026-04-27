<?php

namespace Modules\Purchase\Database\Seeders;

use App\Models\Company;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Purchase\Entities\PurchaseVendor;
use Modules\Purchase\Entities\PurchaseVendorCredit;

class VendorCreditTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run($companyId)
    {
        $faker = Factory::create();
        $vendorId = PurchaseVendor::where('company_id', $companyId)->pluck('id');
        $setting = Company::find($companyId);

        for ($i = 0; $i < 5; $i++) {
            $vendorCredit = new PurchaseVendorCredit();
            $vendorCredit->company_id = $companyId;
            $vendorCredit->vendor_id = $vendorId->random();
            $vendorCredit->credit_note_no = $faker->unique()->numberBetween(1, 10);
            $vendorCredit->credit_date = now()->format('Y-m-d');
            $vendorCredit->currency_id = $setting->currency_id;
            $vendorCredit->discount_type = $faker->randomElement(['percent', 'fixed']);
            $vendorCredit->total = rand(100, 1000);
            $vendorCredit->status = $faker->randomElement(['open']);
            $vendorCredit->calculate_tax = $faker->randomElement(['after_discount', 'before_discount']);
            $vendorCredit->save();
        }
    }

}
