<?php

namespace Modules\Purchase\Observers;

use App\Helper\Files;
use Modules\Purchase\Entities\PurchaseBill;
use Modules\Purchase\Events\VendorCreditEvent;
use Modules\Purchase\Entities\PurchaseVendorItem;
use Modules\Purchase\Entities\PurchaseVendorCredit;
use Modules\Purchase\Entities\PurchaseVendorCreditHistory;
use Modules\Purchase\Entities\PurchaseVendorHistory;
use Modules\Purchase\Entities\PurchaseVendorCreditItemImage;

class PurchaseVendorCreditObserver
{

    public function saving(PurchaseVendorCredit $vendorCredit)
    {

        if (request()->has('calculate_tax')) {
            $vendorCredit->calculate_tax = request()->calculate_tax;
        }
    }

    public function creating(PurchaseVendorCredit $vendorCredit)
    {
        $vendorCredit->hash = md5(microtime());


        if (!isRunningInConsoleOrSeeding()) {
            if (user()) {
                $vendorCredit->added_by = user()->id;
            }
        }

        if (company()) {
            $vendorCredit->company_id = company()->id;
        }

        if ((request()->type && request()->type == 'send' || request()->type == 'mark_as_send')) {
            $vendorCredit->send_status = 1;
        }
        else {
            $vendorCredit->send_status = 0;
        }
    }

    public function created(PurchaseVendorCredit $vendorCredit)
    {
        if (!isRunningInConsoleOrSeeding()) {

            if($vendorCredit->isDirty('vendor_id')){
                event(new VendorCreditEvent($vendorCredit, $vendorCredit->vendors ));
            }

            if (!empty(request()->item_name)){
                $itemSummary = request()->item_summary;
                $cost_per_item = request()->cost_per_item;
                $hsn_sac_code = request()->hsn_sac_code;
                $quantity = request()->quantity;
                $unitId = request()->unit_id;
                $product = request()->product_id;
                $amount = request()->amount;
                $tax = request()->taxes;
                $invoice_item_image = request()->invoice_item_image;
                $invoice_item_image_url = request()->invoice_item_image_url;
                $invoiceOldImage = request()->image_id;

                foreach (request()->item_name as $key => $item) :
                    if (!is_null($item)) {
                        $vendorCreditItem = PurchaseVendorItem::create(
                            [
                                'credit_id' => $vendorCredit->id,
                                'item_name' => $item,
                                'item_summary' => $itemSummary[$key],
                                'type' => 'item',
                                'unit_id' => (isset($unitId[$key]) && !is_null($unitId[$key])) ? $unitId[$key] : null,
                                'product_id' => (isset($product[$key]) && !is_null($product[$key])) ? $product[$key] : null,
                                'hsn_sac_code' => (isset($hsn_sac_code[$key]) && !is_null($hsn_sac_code[$key])) ? $hsn_sac_code[$key] : null,
                                'quantity' => $quantity[$key],
                                'unit_price' => round($cost_per_item[$key], 2),
                                'amount' => round($amount[$key], 2),
                                'taxes' => ($tax ? (array_key_exists($key, $tax) ? json_encode($tax[$key]) : null) : null)
                            ]
                        );
                    }

                    if (isset($vendorCreditItem) && (isset($invoice_item_image[$key]) || isset($invoice_item_image_url[$key]))) {

                        $filename = '';

                        if (isset($invoice_item_image[$key])) {
                            $filename = Files::uploadLocalOrS3($invoice_item_image[$key], PurchaseVendorCreditItemImage::FILE_PATH . '/' . $vendorCreditItem->id . '/');
                        }

                        PurchaseVendorCreditItemImage::create(
                            [
                                'vendor_item_id' => $vendorCreditItem->id,
                                'filename' => !isset($invoice_item_image_url[$key]) ? $invoice_item_image[$key]->getClientOriginalName() : '',
                                'hashname' => !isset($invoice_item_image_url[$key]) ? $filename : '',
                                'size' => !isset($invoice_item_image_url[$key]) ? $invoice_item_image[$key]->getSize() : '',
                                'external_link' => isset($invoice_item_image_url[$key]) ? $invoice_item_image_url[$key] : ''
                            ]
                        );
                    }

                    $image = true;

                    if(isset($invoice_item_image_delete[$key]))
                    {
                        $image = false;
                    }

                    if($image && (isset(request()->image_id[$key]) && $invoiceOldImage[$key] != ''))
                    {
                        $estimateOldImg = PurchaseVendorCreditItemImage::where('id', request()->image_id[$key])->first();

                        if (isset($vendorCreditItem)) {
                            $this->duplicateImageStore($estimateOldImg, $vendorCreditItem);
                        }
                    }

                endforeach;

            }

            if (request()->billId)
            {

                $billId = PurchaseBill::find(request()->billId);

                $billId->credit_note = 1;

                $billId->save();

            }

            $vendorID = request()->vendor_id;

            $this->logVendorActivity(company()->id, $vendorID, $vendorCredit->id, $vendorCredit->total, user()->id, 'vendorCreditCreated', 'Created');

        }
    }

    public function updating(PurchaseVendorCredit $vendorCredit)
    {
        if (!isRunningInConsoleOrSeeding()) {
            if (request()->type && request()->type == 'send' || request()->type == 'mark_as_send') {
                $vendorCredit->send_status = 1;
            }
        }
    }

    public function updated(PurchaseVendorCredit $vendorCredit)
    {
        if (!isRunningInConsoleOrSeeding()) {
            if (request()->bill_id) {

                event(new VendorCreditEvent($vendorCredit, $vendorCredit->vendors ));

                $request = request();

                $items = $request->item_name;
                $itemsSummary = $request->item_summary;
                $hsn_sac_code = $request->hsn_sac_code;
                $tax = $request->taxes;
                $quantity = $request->quantity;
                $unitId = $request->unit_id;
                $productId = $request->product_id;
                $cost_per_item = $request->cost_per_item;
                $amount = $request->amount;
                $proposal_item_image = $request->invoice_item_image;
                $proposal_item_image_url = $request->invoice_item_image_url;
                $item_ids = $request->item_ids;

                if (!empty($request->item_name) && is_array($request->item_name)) {
                    // Step1 - Delete all invoice items which are not avaialable
                    if (!empty($item_ids)) {
                        PurchaseVendorItem::whereNotIn('id', $item_ids)->where('credit_id', $vendorCredit->id)->delete();
                    }

                    // Step2&3 - Find old invoices items, update it and check if images are newer or older
                    foreach ($items as $key => $item) {
                        $invoice_item_id = isset($item_ids[$key]) ? $item_ids[$key] : 0;

                        $vendorCreditItem = PurchaseVendorItem::find($invoice_item_id);

                        if ($vendorCreditItem === null) {
                            $vendorCreditItem = new PurchaseVendorItem();
                        }

                        $vendorCreditItem->credit_id = $vendorCredit->id;
                        $vendorCreditItem->item_name = $item;
                        $vendorCreditItem->item_summary = $itemsSummary[$key];
                        $vendorCreditItem->type = 'item';
                        $vendorCreditItem->unit_id = (isset($unitId[$key]) && !is_null($unitId[$key])) ? $unitId[$key] : null;
                        $vendorCreditItem->product_id = (isset($productId[$key]) && !is_null($productId[$key])) ? $productId[$key] : null;
                        $vendorCreditItem->hsn_sac_code = (isset($hsn_sac_code[$key]) && !is_null($hsn_sac_code[$key])) ? $hsn_sac_code[$key] : null;
                        $vendorCreditItem->quantity = $quantity[$key];
                        $vendorCreditItem->unit_price = round($cost_per_item[$key], 2);
                        $vendorCreditItem->amount = round($amount[$key], 2);
                        $vendorCreditItem->taxes = ($tax ? (array_key_exists($key, $tax) ? json_encode($tax[$key]) : null) : null);
                        $vendorCreditItem->save();


                        /* Invoice file save here */
                        // phpcs:ignore
                        if ((isset($proposal_item_image[$key]) && $request->hasFile('invoice_item_image.' . $key)) || isset($proposal_item_image_url[$key])) {

                            $filename = '';
                            $proposalFileSize = null;

                            /* Delete previous uploaded file if it not a product (because product images cannot be deleted) */
                            if (!isset($proposal_item_image_url[$key]) && $vendorCreditItem && $vendorCreditItem->purchaseVendorCreditItemImage) {
                                Files::deleteFile($vendorCreditItem->purchaseVendorCreditItemImage->hashname, PurchaseVendorCreditItemImage::FILE_PATH . '/' . $vendorCreditItem->id . '/');

                                $filename = Files::uploadLocalOrS3($proposal_item_image[$key], PurchaseVendorCreditItemImage::FILE_PATH . '/' . $vendorCreditItem->id . '/');
                                $proposalFileSize = $proposal_item_image[$key]->getSize();
                            }

                            if ($filename == '') {
                                $filename = Files::uploadLocalOrS3($proposal_item_image[$key], PurchaseVendorCreditItemImage::FILE_PATH . '/' . $vendorCreditItem->id . '/');
                            }

                            PurchaseVendorCreditItemImage::updateOrCreate(
                                [
                                    'vendor_item_id' => $vendorCreditItem->id,
                                ],
                                [
                                    'filename' => !isset($proposal_item_image_url[$key]) ? $proposal_item_image[$key]->getClientOriginalName() : '',
                                    'hashname' => !isset($proposal_item_image_url[$key]) ? $filename : '',
                                    'size' => !isset($proposal_item_image_url[$key]) ? $proposalFileSize : '',
                                    'external_link' => $proposal_item_image_url[$key] ?? ''
                                ]
                            );
                        }
                    }
                }
            }
        }

        $vendorID = request()->vendor_id;

        $this->logVendorActivity(company()->id, $vendorID, $vendorCredit->id, $vendorCredit->total, user()->id, 'vendorCreditUpdated', 'Updated');

    }

    public function duplicateImageStore($estimateOldImg, $vendorCreditItem)
    {
        if(!is_null($estimateOldImg)) {

            $file = new PurchaseVendorItem();

            $file->vendor_item_id = $vendorCreditItem->id;

            $fileName = Files::generateNewFileName($estimateOldImg->filename);

            Files::copy(PurchaseVendorCreditItemImage::FILE_PATH . '/' . $estimateOldImg->id . '/' . $estimateOldImg->hashname, PurchaseVendorItem::FILE_PATH . '/' . $vendorCreditItem->id . '/' . $fileName);

            $file->filename = $estimateOldImg->filename;
            $file->hashname = $fileName;
            $file->size = $estimateOldImg->size;
            $file->save();

        }
    }

    public function logVendorActivity($companyID, $vendorID, $creditID, $amount, $userID, $text, $label)
    {
        $activiy = new PurchaseVendorCreditHistory();

        $activiy->company_id = $companyID;
        $activiy->purchase_vendor_id = $vendorID;
        $activiy->purchase_credit_id = $creditID;
        $activiy->amount = $amount;
        $activiy->user_id = $userID;
        $activiy->details = $text;
        $activiy->label = $label;

        $activiy->save();
    }

}
