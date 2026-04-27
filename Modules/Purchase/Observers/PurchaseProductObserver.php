<?php

namespace Modules\Purchase\Observers;

use App\Traits\UnitTypeSaveTrait;
use Modules\Purchase\Entities\PurchaseProduct;
use Modules\Purchase\Entities\PurchaseProductHistory;

class PurchaseProductObserver
{

    use UnitTypeSaveTrait;

    public function saving(PurchaseProduct $product)
    {
        $this->unitType($product);

        if (!isRunningInConsoleOrSeeding()) {
            $product->last_updated_by = user() ? user()->id : null;
        }
    }

    public function creating(PurchaseProduct $product)
    {
        if (!isRunningInConsoleOrSeeding()) {
            $product->added_by = user() ? user()->id : null;
        }

        if (company()) {
            $product->company_id = company()->id;
        }
    }

    public function created(PurchaseProduct $product)
    {
        if (!isRunningInConsoleOrSeeding()) {
            $this->productHistoryActivity(company()->id, $product->id, user()->id, 'Created', 'productCreated', $product->type);
        }
    }

    public function updated(PurchaseProduct $product)
    {
        if (!isRunningInConsoleOrSeeding()) {
            $this->productHistoryActivity(company()->id, $product->id, user()->id, 'Updated', 'productUpdated', $product->type);
        }
    }

    public function deleting(PurchaseProduct $product)
    {
        $product->files()->each(function ($file) {
            $file->delete();
        });
    }

    public function productHistoryActivity($companyID, $productID, $userID, $label, $details, $type)
    {
        $activities = new PurchaseProductHistory();
        $activities->company_id = $companyID;
        $activities->purchase_product_id = $productID;
        $activities->user_id = $userID;
        $activities->label = $label;
        $activities->details = $details;
        $activities->type = $type;
        $activities->save();
    }

}
