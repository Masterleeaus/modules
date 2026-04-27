<?php

namespace Modules\Purchase\Database\Seeders;

use Faker\Factory;
use Illuminate\Database\Seeder;
use Modules\Purchase\Entities\PurchaseBill;
use Modules\Purchase\Entities\PurchasePaymentBill;
use Modules\Purchase\Entities\PurchaseVendor;
use Modules\Purchase\Entities\PurchaseVendorPayment;

class PaymentsTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run($companyId)
    {
        $bills = PurchaseBill::with('vendor')->where('company_id', $companyId)->get();

        foreach($bills as $bill){

            $payment = [
                'company_id' => $companyId,
                'purchase_vendor_id' => $bill->vendor->id,
                'payment_date' => now()->format('Y-m-d'),
                'received_payment' => $bill->total,
                'excess_payment' => 0,
            ];

            PurchaseVendorPayment::create($payment);

            $paymentID = PurchaseVendorPayment::where('company_id', $companyId)->pluck('id');

            $paymentBill = [
                'purchase_vendor_payment_id' => $paymentID->random(),
                'purchase_vendor_id' => $bill->vendor->id,
                'purchase_bill_id' => $bill->id,
                'total_paid' => $bill->total
            ];

            PurchasePaymentBill::create($paymentBill);

        }
    }

}
