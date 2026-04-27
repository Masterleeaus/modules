<?php

namespace Modules\Purchase\Observers;

use Exception;
use App\Helper\Files;
use Modules\Purchase\Entities\PurchaseItem;
use Modules\Purchase\Entities\PurchaseOrder;
use Modules\Purchase\Entities\PurchaseItemTax;
use Modules\Purchase\Entities\PurchaseItemImage;
use Modules\Purchase\Events\NewPurchaseOrderEvent;
use Modules\Purchase\Entities\PurchaseOrderHistory;
use Modules\Purchase\Entities\PurchaseStockAdjustment;

class PurchaseOrderObserver
{

    public function saving(PurchaseOrder $order)
    {
        if (!isRunningInConsoleOrSeeding()) {

            if (user()) {
                $order->last_updated_by = user()->id;

                if (request()->has('calculate_tax')) {
                    $order->calculate_tax = request()->calculate_tax;
                }
            }
        }
    }

    public function creating(PurchaseOrder $order)
    {

        if (!isRunningInConsoleOrSeeding()) {

            if ((request()->type && request()->type == 'send') || request()->type == 'mark_as_send') {
                $order->send_status = 1;
            } else {
                $order->send_status = 0;
            }

            if (request()->type && request()->type == 'draft') {
                $order->purchase_status = 'draft';
            }
        }

        if (company()) {
            $order->company_id = company()->id;
        }

        $order->added_by = user() ? user()->id : null;
    }

    public function created(PurchaseOrder $order)
    {
        if (!isRunningInConsoleOrSeeding()) {

            if ($order->vendor && request()->type && request()->type == 'send') {
                event(new NewPurchaseOrderEvent($order, $order->vendor));
            }

            if (!empty(request()->item_name) && is_array(request()->item_name)) {

                $itemsSummary = request()->item_summary;
                $cost_per_item = request()->cost_per_item;
                $hsn_sac_code = request()->hsn_sac_code;
                $quantity = request()->quantity;
                $unitId = request()->unit_id;
                $product = request()->product_id;
                $amount = request()->amount;
                $order_item_image = request()->order_item_image;
                $order_item_image_delete = request()->order_item_image_delete;
                $order_item_image_url = request()->order_item_image_url;
                $orderOldImage = request()->image_id;

                foreach (request()->item_name as $key => $item) :
                    if (!is_null($item)) {
                        $orderItem = PurchaseItem::create(
                            [
                                'purchase_order_id' => $order->id,
                                'item_name' => $item,
                                'item_summary' => $itemsSummary[$key] ?: '',
                                'type' => 'item',
                                'unit_id' => (isset($unitId[$key]) && !is_null($unitId[$key])) ? $unitId[$key] : null,
                                'product_id' => (isset($product[$key]) && !is_null($product[$key])) ? $product[$key] : null,
                                'hsn_sac_code' => (isset($hsn_sac_code[$key]) && !is_null($hsn_sac_code[$key])) ? $hsn_sac_code[$key] : null,
                                'quantity' => $quantity[$key],
                                'unit_price' => round($cost_per_item[$key], 2),
                                'amount' => round($amount[$key], 2),
                            ]
                        );

                        if ($order->delivery_status === 'delivered') {
                            $inventory = PurchaseStockAdjustment::where('product_id', $product[$key])->first();

                            // If $inventory is null, initialize a new PurchaseStockAdjustment object
                            if (!$inventory) {
                                $inventory = new PurchaseStockAdjustment();
                                $inventory->product_id = $product[$key];
                                $inventory->net_quantity = 0; // Set the initial net quantity to 0
                            }

                            // Add the quantity to the net_quantity
                            $inventory->net_quantity += $quantity[$key];
                            $inventory->save();
                        }
                    }

                    if (isset(request()->taxes[$key])) {
                        foreach (request()->taxes[$key] as $tax) {
                            $item = PurchaseItemTax::create([
                                'purchase_order_id' => $order->id,
                                'purchase_item_id' => $orderItem->id,
                                'tax_id' => $tax,
                            ]);
                        }
                    }

                    /* order file save here */
                    if (isset($orderItem) && (isset($order_item_image[$key]) || isset($order_item_image_url[$key]))) {

                        $filename = '';

                        if (isset($order_item_image[$key])) {
                            $filename = Files::uploadLocalOrS3($order_item_image[$key], PurchaseItemImage::FILE_PATH . '/' . $orderItem->id . '/');
                        }

                        $var = PurchaseItemImage::create(
                            [
                                'purchase_item_id' => $orderItem->id,
                                'filename' => !isset($order_item_image_url[$key]) ? $order_item_image[$key]->getClientOriginalName() : '',
                                'hashname' => !isset($order_item_image_url[$key]) ? $filename : '',
                                'size' => !isset($order_item_image_url[$key]) ? $order_item_image[$key]->getSize() : '',
                                'external_link' => isset($order_item_image_url[$key]) ? $order_item_image_url[$key] : ''
                            ]
                        );
                    }

                endforeach;
            }

            $this->logOrderActivity(company()->id, $order->id, $order->vendor_id, user()->id, 'purchaseOrderCreated', 'Created');
        }
    }

    public function updating(PurchaseOrder $order)
    {
        if (!isRunningInConsoleOrSeeding()) {
            if (request()->type && request()->type == 'send' || request()->type == 'mark_as_send') {
                $order->send_status = 1;
            }
        }
    }

    public function updated(PurchaseOrder $order)
    {

        foreach ($order->items as $item) {



            if ($order->delivery_status === 'delivered') {
                $inventory = PurchaseStockAdjustment::where('product_id', $item->product_id)->first();

                // If $inventory is null, initialize a new PurchaseStockAdjustment object
                if (!$inventory) {
                    $inventory = new PurchaseStockAdjustment();
                    $inventory->product_id = $item->product_id;
                    $inventory->net_quantity = 0; // Set the initial net quantity to 0
                }

                // Add the quantity to the net_quantity
                $inventory->net_quantity += $item->quantity;
                $inventory->save();
            }
        }
        if (!isRunningInConsoleOrSeeding()) {
            /*
                Step1 - Delete all orders items which are not available
                Step2 - Find old orders items, update it and check if images are newer or older
                Step3 - Insert new orders items with images
            */

            $request = request();

            $items = $request->item_name;
            $itemsSummary = $request->item_summary;
            $hsn_sac_code = $request->hsn_sac_code;
            $unitId = $request->unit_id;
            $product = $request->product_id;
            $quantity = $request->quantity;
            $cost_per_item = $request->cost_per_item;
            $amount = $request->amount;
            $order_item_image = $request->order_item_image;
            $order_item_image_url = $request->order_item_image_url;
            $item_ids = $request->item_ids;

            if (!empty($request->item_name) && is_array($request->item_name)) {
                // Step1 - Delete all order items which are not avaialable
                if (!empty($item_ids)) {
                    PurchaseItem::where('purchase_order_id', $order->id)->delete();
                }

                // Step2&3 - Find old invoices items, update it and check if images are newer or older
                foreach ($items as $key => $item) {
                    $invoice_item_id = isset($item_ids[$key]) ? $item_ids[$key] : 0;

                    try {
                        $orderItem = PurchaseItem::findOrFail($invoice_item_id);
                    } catch (Exception) {
                        $orderItem = new PurchaseItem();
                    }

                    $orderItem->purchase_order_id = $order->id;
                    $orderItem->item_name = $item;
                    $orderItem->item_summary = $itemsSummary[$key];
                    $orderItem->type = 'item';
                    $orderItem->unit_id = (isset($unitId[$key]) && !is_null($unitId[$key])) ? $unitId[$key] : null;
                    $orderItem->product_id = (isset($product[$key]) && !is_null($product[$key])) ? $product[$key] : null;
                    $orderItem->hsn_sac_code = (isset($hsn_sac_code[$key]) && !is_null($hsn_sac_code[$key])) ? $hsn_sac_code[$key] : null;
                    $orderItem->quantity = $quantity[$key];
                    $orderItem->unit_price = round($cost_per_item[$key], 2);
                    $orderItem->amount = round($amount[$key], 2);
                    $orderItem->company_id = company()->id;
                    $orderItem->saveQuietly();

                    if (isset(request()->taxes[$key])) {
                        foreach (request()->taxes[$key] as $tax) {
                            $exitingTax = PurchaseItemTax::where([
                                ['tax_id', '=', $tax],
                                ['purchase_order_id', '=',  $order->id],
                                ['purchase_item_id', '=', $orderItem->id]
                            ])->exists();

                            if (!$exitingTax) {
                                $item = PurchaseItemTax::create([
                                    'purchase_order_id' => $order->id,
                                    'purchase_item_id' => $orderItem->id,
                                    'tax_id' => $tax,
                                ]);
                            }
                        }
                    }

                    /* order file save here */
                    if ((isset($order_item_image[$key]) && $request->hasFile('order_item_image.' . $key)) || isset($order_item_image_url[$key])) {

                        /* Delete previous uploaded file if it not a product (because product images cannot be deleted) */
                        if (!isset($order_item_image_url[$key]) && $orderItem && $orderItem->purchaseItemImage) {
                            Files::deleteFile($orderItem->purchaseItemImage->hashname, PurchaseItemImage::FILE_PATH . '/' . $orderItem->id . '/');
                        }

                        $filename = '';

                        if (isset($order_item_image[$key])) {
                            $filename = Files::uploadLocalOrS3($order_item_image[$key], PurchaseItemImage::FILE_PATH . '/' . $orderItem->id . '/');
                        }

                        PurchaseItemImage::updateOrCreate(
                            [
                                'purchase_item_id' => $orderItem->id,
                            ],
                            [
                                'filename' => !isset($order_item_image_url[$key]) ? $order_item_image[$key]->getClientOriginalName() : '',
                                'hashname' => !isset($order_item_image_url[$key]) ? $filename : '',
                                'size' => !isset($order_item_image_url[$key]) ? $order_item_image[$key]->getSize() : '',
                                'external_link' => isset($order_item_image_url[$key]) ? $order_item_image_url[$key] : ''
                            ]
                        );
                    }
                }
            }
        }

        $this->logOrderActivity(company()->id, $order->id, $order->vendor_id, user()->id, 'purchaseOrderUpdated', 'Updated');

        $order->saveQuietly();
    }

    public function deleting(PurchaseOrder $order)
    {
        /* Delete invoice item files */
        $orderItems = PurchaseItem::where('purchase_order_id', $order->id)->get();

        if ($orderItems) {
            foreach ($orderItems as $orderItem) {
                Files::deleteDirectory(PurchaseItemImage::FILE_PATH . '/' . $orderItem->id);
            }
        }
    }

    public function logOrderActivity($companyID, $orderID, $vendorID, $userID, $text, $label)
    {
        $activiy = new PurchaseOrderHistory();

        $activiy->company_id = $companyID;
        $activiy->purchase_order_id = $orderID;
        $activiy->purchase_vendor_id = $vendorID;
        $activiy->user_id = $userID;
        $activiy->details = $text;
        $activiy->label = $label;

        $activiy->save();
    }
}
