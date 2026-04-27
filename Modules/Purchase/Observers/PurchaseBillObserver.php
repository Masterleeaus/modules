<?php

namespace Modules\Purchase\Observers;

use Modules\Purchase\Entities\PurchaseBill;
use Modules\Purchase\Entities\PurchaseBillHistory;
use Modules\Purchase\Entities\PurchaseOrder;
// use Modules\Purchase\Entities\PurchaseVendorHistory;
use Modules\Purchase\Events\NewPurchaseBillEvent;

class PurchaseBillObserver
{

    public function creating(PurchaseBill $bill)
    {
        if (!isRunningInConsoleOrSeeding() && user()) {
            $bill->added_by = user()->id;
        }

        if (company()) {
            $bill->company_id = company()->id;
        }
    }

    public function created(PurchaseBill $bill)
    {
        if (!isRunningInConsoleOrSeeding()) {
            event(new NewPurchaseBillEvent($bill));
        }

        if(request()->purchase_order_id)
        {
            PurchaseOrder::where('id', request()->purchase_order_id)->update([ 'billed_status' => 'billed']);
        }

        $vendorID = request()->vendor_id;
        $order = PurchaseOrder::where('id', $bill->purchase_order_id)->first();
        $purchaseOrderNo = $order->purchase_order_number;

        if (! isRunningInConsoleOrSeeding()) {
            if (\user()) {
                $this->logBillActivity(company()->id, $vendorID, $bill->id, request()->purchase_order_id, $purchaseOrderNo, $bill->total, user()->id, $bill->bill_date, 'billCreated', 'Created');
            }
        }

    }

    public function updated(PurchaseBill $purchaseBill)
    {
        $vendorID = request()->vendor_id;

        $this->logBillActivity(company()->id, $vendorID, $purchaseBill->id, request()->purchase_order_id, null, null, null, $purchaseBill->bill_Date, 'billUpdated', 'Updated');

    }

    public function logBillActivity($companyID, $vendorID, $billID, $purchaseOrderId, $purchaseOrderNo, $amount, $userID, $billDate, $text, $label)
    {
        $activity = new PurchaseBillHistory();

        $activity->company_id = $companyID;
        $activity->purchase_vendor_id = $vendorID;
        $activity->purchase_bill_id = $billID;
        $activity->purchase_order_id = $purchaseOrderId;
        $activity->purchase_order = $purchaseOrderNo;
        $activity->amount = $amount;
        $activity->user_id = $userID;
        $activity->bill_date = $billDate;
        $activity->details = $text;
        $activity->label = $label;

        $activity->save();
    }

}
