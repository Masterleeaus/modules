<?php

namespace Modules\Purchase\Database\Seeders;

use Faker\Factory;
use Illuminate\Database\Seeder;
use Modules\Purchase\Entities\PurchaseBill;
use Modules\Purchase\Entities\PurchaseOrder;
use Modules\Purchase\Entities\PurchaseVendor;

class BillsTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run($companyId)
    {
        $faker = Factory::create();
        $orders = PurchaseOrder::with('vendor')->where('company_id', $companyId)->get();
        
        foreach($orders as $order){
            $bill = [
                'purchase_bill_number' => $faker->randomDigit,
                'company_id' => $companyId,
                'purchase_vendor_id' => $order->vendor->id,
                'bill_date' => now()->format('Y-m-d'),
                'purchase_order_id' => $order->id,
                'discount' => 0.00,
                'sub_total' => $order->total,
                'total' => $order->total,
                'discount_type' => 'percent',
                'status' => 'open',
            ];

            PurchaseBill::create($bill);
        }
    }

}