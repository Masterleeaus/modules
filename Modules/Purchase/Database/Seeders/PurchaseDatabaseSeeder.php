<?php

namespace Modules\Purchase\Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Modules\Purchase\Entities\PurchaseSetting;
use Modules\Purchase\Entities\PurchaseStockAdjustmentReason;
use Illuminate\Support\Facades\App;

class PurchaseDatabaseSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        config(['app.seeding' => true]);

        $companies = Company::all();

        foreach ($companies as $company) {
            $this->purchaseSettingSeeder($company->id);
            $this->stockAdjustmentReasonSeeder($company->id);

            if (! App::environment('codecanyon')) {
                $this->call(VendorsTableSeeder::class, false, ['companyId' => $company->id]);
                $this->call(PurchaseOrdersTableSeeder::class, false, ['companyId' => $company->id]);
                $this->call(BillsTableSeeder::class, false, ['companyId' => $company->id]);
                $this->call(PaymentsTableSeeder::class, false, ['companyId' => $company->id]);
                $this->call(InventoryTableSeeder::class, false, ['companyId' => $company->id]);
                $this->call(VendorCreditTableSeeder::class, false, ['companyId' => $company->id]);
            }

        }

        config(['app.seeding' => false]);
    }

    public function purchaseSettingSeeder($companyId)
    {
        $purchaseSetting = new PurchaseSetting();
        $purchaseSetting->company_id = $companyId;
        $purchaseSetting->save();
    }

    public function purchaseNotificationSeeder($companyId)
    {
        PurchaseSetting::insertNotification($companyId);
    }

    public function stockAdjustmentReasonSeeder($companyId)
    {
        $purchaseSetting = new PurchaseStockAdjustmentReason();
        $purchaseSetting->company_id = $companyId;
        $purchaseSetting->name = 'Stock on fire';
        $purchaseSetting->save();

        $purchaseSetting = new PurchaseStockAdjustmentReason();
        $purchaseSetting->company_id = $companyId;
        $purchaseSetting->name = 'Stolen';
        $purchaseSetting->save();

        $purchaseSetting = new PurchaseStockAdjustmentReason();
        $purchaseSetting->company_id = $companyId;
        $purchaseSetting->name = 'flood';
        $purchaseSetting->save();

        $purchaseSetting = new PurchaseStockAdjustmentReason();
        $purchaseSetting->company_id = $companyId;
        $purchaseSetting->name = 'destroyed';
        $purchaseSetting->save();
    }

}
