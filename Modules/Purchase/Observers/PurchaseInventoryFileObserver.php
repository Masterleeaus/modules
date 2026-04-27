<?php

namespace Modules\Purchase\Observers;

use App\Helper\Files;
use Modules\Purchase\Entities\PurchaseInventoryFile;
use Modules\Purchase\Entities\PurchaseInventoryHistory;

class PurchaseInventoryFileObserver
{

    /**
     * @param PurchaseInventoryFile $item
     */
    public function saving(PurchaseInventoryFile $inventoryFiles)
    {
        if (!isRunningInConsoleOrSeeding() && user()) {
            $inventoryFiles->last_updated_by = user()->id;
        }
    }

    public function creating(PurchaseInventoryFile $inventoryFiles)
    {
        if (!isRunningInConsoleOrSeeding() && user()) {
            $inventoryFiles->added_by = user()->id;
        }

        if (company()) {
            $inventoryFiles->company_id = company()->id;
        }
    }

    public function created(PurchaseInventoryFile $event)
    {
        if (!isRunningInConsoleOrSeeding()) {
            if (\user()) {
                $this->logInventoryActivity(company()->id, $event->inventory_id, user()->id, null, null, null, null, null, 'inventoryFileCreated', 'fileCreated', $event->id);
            }
        }
    }

    public function deleting(PurchaseInventoryFile $inventoryFiles)
    {
        $inventoryFiles->load('product');

        if (!isRunningInConsoleOrSeeding()) {
            if (isset($inventoryFiles->product) && $inventoryFiles->product->default_image == $inventoryFiles->hashname) {
                $inventoryFiles->product->default_image = null;
                $inventoryFiles->product->save();
            }
        }

        Files::deleteFile($inventoryFiles->hashname, PurchaseInventoryFile::FILE_PATH);
    }

    public function logInventoryActivity($companyID, $inventoryID, $userID, $productName, $netQuantity, $quantityAdjustment, $changedValue, $adjustedValue, $text, $label, $inventoryFilesID = null)
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

        if (!is_null($inventoryFilesID))
        {
            $activity->purchase_inventory_files_id = $inventoryFilesID;
        }

        $activity->save();
    }

}
