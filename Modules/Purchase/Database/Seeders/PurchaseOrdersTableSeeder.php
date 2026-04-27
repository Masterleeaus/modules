<?php

namespace Modules\Purchase\Database\Seeders;

use App\Models\BankAccount;
use App\Models\Company;
use App\Models\CompanyAddress;
use App\Models\Product;
use App\Models\UnitType;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Modules\Purchase\Entities\PurchaseItem;
use Modules\Purchase\Entities\PurchaseOrder;
use Modules\Purchase\Entities\PurchaseVendor;

class PurchaseOrdersTableSeeder extends Seeder
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
        $bankAccountId = BankAccount::where('company_id', $companyId)->inRandomOrder()->first()->id;
        $companyAddress = CompanyAddress::where('is_default', 1)->firstOrFail();
        $setting = Company::find($companyId);
        $unit = UnitType::select('id')->where('company_id', $companyId)->first();

        foreach(range(0, 15) as $item){
            $product = Product::all()->random();
            $purchaseOrder = $item + 1;
            $order = [
                'purchase_order_number' => $purchaseOrder,
                'company_id' => $companyId,
                'vendor_id' => $vendorId->random(),
                'bank_account_id' => $bankAccountId,
                'address_id' => $companyAddress->id,
                'currency_id' => $setting->currency_id,
                'default_currency_id' => $setting->currency_id,
                'exchange_rate' => 1.00,
                'purchase_date' => now()->format('Y-m-'.rand(1, 30)),
                'note' => null,
                'expected_delivery_date' => now()->addDays('10')->format('Y-m-d H:i:s'),
                'discount' => 0.00,
                'discount_type' => 'percent',
                'send_status' => 0,
                'sub_total' => $product->price,
                'total' => $product->price,
                'purchase_status' => 'open',
                'billed_status' => 'billed',
                'delivery_status' => 'not_started',
                'calculate_tax' => 'after_discount',
            ];

            $purchaseOrder = PurchaseOrder::create($order);

            $this->purchaseOrderItems($purchaseOrder, $product, $companyId, $unit);

        }
    }

    public function purchaseOrderItems($order, $product, $companyId, $unit)
    {
        $item = [
            'company_id' => $companyId,
            'purchase_order_id' => $order->id,
            'unit_id' => $unit->id,
            'product_id' => $product->id,
            'item_name' => $product->name,
            'item_summary' => $product->description,
            'type' => 'item',
            'quantity' => '1.00',
            'unit_price' => $product->price,
            'amount' => $product->price,
            'hsn_sac_code' => null,
        ];

        PurchaseItem::create($item);
    }

}
