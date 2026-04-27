<?php

namespace Modules\Purchase\Observers;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use Modules\Purchase\Entities\PurchaseBill;
use Modules\Purchase\Entities\PurchaseVendorItem;
use Modules\Purchase\Events\NewVendorPaymentEvent;
use Modules\Purchase\Entities\PurchaseVendorPayment;
use Modules\Purchase\Entities\PurchasePaymentHistory;
use Modules\Purchase\Events\UpdateVendorPaymentEvent;

class PurchaseVendorPaymentObserver
{

    public function creating(PurchaseVendorPayment $model)
    {
        if (!isRunningInConsoleOrSeeding() && user()) {
            $model->added_by = user()->id;
        }

        if (company()) {
            $model->company_id = company()->id;
        }
    }

    public function created(PurchaseVendorPayment $payment)
    {
        if (!isRunningInConsoleOrSeeding()) {
            event(new NewVendorPaymentEvent($payment));

            $amounts = array_combine(request()->bill_id, request()->amount_paid_per);

            foreach($amounts as $key => $amt){

                $bill = PurchaseBill::with('order')->where('id', $key)->first();
                $purchaseOrderID = $bill->purchase_order_id;
                $purchaseOrderNo = $bill->order->purchase_order_number;

            }

            $vendorID = request()->vendor_id;

            $this->logVendorPaymentActivity(company()->id, $vendorID, $payment->id, user()->id, $purchaseOrderID, $purchaseOrderNo, $key, $amt, 'vendorPaymentCreated', 'Created');

        }

        if(!is_null($payment->bank_account_id)){
            $bankAccount = BankAccount::find($payment->bank_account_id);
            $bankBalance = $bankAccount->bank_balance;
            $totalBalance = $bankBalance - $payment->received_payment;

            $transaction = new BankTransaction();
            $transaction->company_id = $payment->company_id;
            $transaction->bank_account_id = $payment->bank_account_id;
            $transaction->purchase_payment_id = $payment->id;
            $transaction->type  = 'Dr';
            $transaction->amount = round($payment->received_payment, 2);
            $transaction->transaction_date = $payment->payment_date;
            $transaction->bank_balance = round($totalBalance);
            $transaction->transaction_relation = 'purchase_payment';
            $transaction->title = 'payment-debited';
            $transaction->save();

            $bankAccount->save();

        }
    }

    public function saving(PurchaseVendorPayment $payment)
    {
        if (!isRunningInConsoleOrSeeding() && user()) {
            $payment->last_updated_by = user()->id;
        }
    }

    public function updated(PurchaseVendorPayment $payment)
    {
        if (!isRunningInConsoleOrSeeding()) {
            event(new UpdateVendorPaymentEvent($payment));

            $amounts = array_combine(request()->bill_id, request()->amount_paid_per);

            foreach($amounts as $key => $amt){
                $bill = PurchaseBill::with('order')->where('id', $key)->first();

                $purchaseOrderID = $bill->purchase_order_id;
                $purchaseOrderNo = $bill->order->purchase_order_number;
            }

            $vendorID = request()->vendor_id;

            $this->logVendorPaymentActivity(company()->id, $vendorID, $payment->id, user()->id, $purchaseOrderID, $purchaseOrderNo, $key, $amt, 'vendorPaymentUpdated', 'Updated');

            if(!is_null($payment->bank_account_id))
            {

                if($payment->isDirty('bank_account_id'))
                {
                    $originalAccount = $payment->getOriginal('bank_account_id');
                    $oldAmount = $payment->getOriginal('received_payment');
                    $newAmount = $payment->received_payment;

                    $bankAccount = BankAccount::find($originalAccount);
                    
                    if($bankAccount){
                        $bankBalance = $bankAccount->bank_balance;
                        $bankBalance += $oldAmount;

                        $transaction = new BankTransaction();
                        $transaction->purchase_payment_id = $payment->id;
                        $transaction->type = 'Cr';
                        $transaction->bank_account_id = $originalAccount;
                        $transaction->amount = round($oldAmount, 2);
                        $transaction->transaction_date = $payment->payment_date;
                        $transaction->bank_balance = round($bankBalance, 2);
                        $transaction->transaction_relation = 'purchase_payment';

                        $transaction->title = 'payment-credited';
                        $transaction->save();

                        $bankAccount->bank_balance = round($bankBalance, 2);
                        $bankAccount->save();
                    }

                    $newBankAccount = BankAccount::find($payment->bank_account_id);

                    if($newBankAccount){
                        $newBankBalance = $newBankAccount->bank_balance;
                        $newBankBalance -= $newAmount;

                        $transaction = new BankTransaction();
                        $transaction->purchase_payment_id = $payment->id;
                        $transaction->type = 'Dr';
                        $transaction->bank_account_id = $payment->bank_account_id;
                        $transaction->amount = round($newAmount, 2);
                        $transaction->transaction_date = $payment->payment_date;
                        $transaction->bank_balance = round($newBankBalance, 2);
                        $transaction->transaction_relation = 'purchase_payment';
                        $transaction->title = 'payment-debited';
                        $transaction->save();

                        $newBankAccount->bank_balance = round($newBankBalance, 2);
                        $newBankAccount->save();
                    }

                }

            }
        }

    }

    public function deleting(PurchaseVendorPayment $payment)
    {
        if(!is_null($payment->bank_account_id)){

            $account = $payment->bank_account_id;
            $amount = $payment->received_payment;

            $bankAccount = BankAccount::find($account);

            if($bankAccount){
                $bankBalance = $bankAccount->bank_balance;
                $bankBalance += $amount;

                $transaction = new BankTransaction();
                $transaction->purchase_payment_id = $payment->id;
                $transaction->type = 'Cr';
                $transaction->bank_account_id = $account;
                $transaction->amount = round($amount, 2);
                $transaction->transaction_date = $payment->payment_date;
                $transaction->bank_balance = round($bankBalance, 2);
                $transaction->transaction_relation = 'purchase_payment';
                $transaction->title = 'payment-deleted';
                $transaction->save();

                $bankAccount->bank_balance = round($bankBalance, 2);
                $bankAccount->save();
            }
        }
    }

    public function logVendorPaymentActivity($companyID, $vendorID, $vendorPaymentID, $userID, $purchaseOrderID, $purchaseOrderNo, $vendorBillID, $amt, $text, $label)
    {

        $activity = new PurchasePaymentHistory();

        $activity->company_id = $companyID;
        $activity->purchase_vendor_id = $vendorID;
        $activity->purchase_payment_id = $vendorPaymentID;
        $activity->user_id = $userID;
        $activity->purchase_order_id = $purchaseOrderID;
        $activity->purchase_order = $purchaseOrderNo;
        $activity->purchase_bill_id = $vendorBillID;
        $activity->amount = $amt;
        $activity->details = $text;
        $activity->label = $label;
        $activity->save();
    }

}
