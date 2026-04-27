<?php

namespace Modules\Purchase\Observers;

use App\Models\InvoiceItems;
use App\Models\Product;
use App\Models\User;
use Modules\Purchase\Entities\PurchaseInventory;
use Modules\Purchase\Entities\PurchaseInventoryHistory;
use Modules\Purchase\Entities\PurchaseStockAdjustment;
use Modules\Purchase\Events\PurchaseInventoryEvent;

class PurchaseInventoryObserver
{

    /**
     * @param PurchaseInventory $item
     */

    public function creating(PurchaseInventory $inventory)
    {
        if (company()) {
            $inventory->company_id = company()->id;
        }
    }

    public function saved(PurchaseInventory $inventory)
    {
        if (request()->inventory_id) {
            $inventoryFile = PurchaseInventoryHistory::where('inventory_id', $inventory->id)->latest()->first();
            $inventoryFileId = $inventoryFile->purchase_inventory_files_id;
        }

        if (!isRunningInConsoleOrSeeding()) {

            if (request()->product_id) {

                $quantity = request()->quantity_on_hand;

                $products = request()->product_id;

                if (is_array($products)) {

                    foreach ($products as $key => $product) {

                        $productName = Product::where('id', $product)->pluck('name')->first();

                        if (!empty($quantity)) {
                            $netQuantity = request()->quantity_on_hand[$key] ?: null;
                            $quantityAdjustment = request()->quantity_adjusted[$key] ?: 0;

                        } else {
                            $changedValue = request()->changed_value ? request()->changed_value[$key] : null;
                            $adjustedValue = request()->adjusted_value[$key] ?: 0;
                        }

                        if (!empty($quantity)) {
                            if (!isRunningInConsoleOrSeeding()) {
                                if (\user()) {
                                    $this->logInventoryActivity(company()->id, $inventory->id, user()->id, $productName, $netQuantity, $quantityAdjustment, null, null, 'inventoryCreated', 'Created');
                                }
                            }
                        } else {
                            if (!isRunningInConsoleOrSeeding()) {
                                if (\user()) {
                                    $this->logInventoryActivity(company()->id, $inventory->id, user()->id, $productName, null, null, $changedValue, $adjustedValue, 'inventoryCreated', 'Created');
                                }
                            }
                        }
                    }
                } else {

                    $productId = request()->product_id;
                    $productName = Product::where('id', $productId)->pluck('name')->first();

                    if (!empty($quantity)) {
                        $netQuantity = request()->quantity_on_hand ?: null;
                        $quantityAdjustment = request()->quantity_adjusted ?: 0;

                    } else {
                        $changedValue = request()->changed_value ? request()->changed_value : null;
                        $adjustedValue = request()->adjusted_value ?: 0;
                    }

                    if (!empty($quantity)) {
                        if (!isRunningInConsoleOrSeeding()) {
                            if (\user()) {
                                $this->logInventoryActivity(company()->id, $inventory->id, user()->id, $productName, $netQuantity, $quantityAdjustment, null, null, 'inventoryCreated', 'Created');
                            }
                        }
                    } else {
                        if (!isRunningInConsoleOrSeeding()) {
                            if (\user()) {
                                $this->logInventoryActivity(company()->id, $inventory->id, user()->id, $productName, null, null, $changedValue, $adjustedValue, 'inventoryCreated', 'Created');
                            }
                        }
                    }
                }

            } else {
                if (!isset($inventoryFileId)) {
                    $this->logInventoryActivity(company()->id, $inventory->id, user()->id, null, null, null, null, null, 'inventoryCreated', 'Created');
                }
            }

        }

    }

    public function logInventoryActivity($companyID, $inventoryID, $userID, $productName, $netQuantity, $quantityAdjustment, $changedValue, $adjustedValue, $text, $label)
    {
        $activity = new PurchaseInventoryHistory();
        $activity->company_id = $companyID;
        $activity->inventory_id = $inventoryID;
        $activity->user_id = $userID;
        $activity->product_name = $productName;
        $activity->net_quantity = $netQuantity;
        $activity->quantity_adjustment = $quantityAdjustment;
        $activity->changed_value = $changedValue;
        $activity->adjusted_value = $adjustedValue;
        $activity->details = $text;
        $activity->label = $label;

        $activity->save();
    }

}
