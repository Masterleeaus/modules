<?php

namespace Modules\Purchase\Observers;

use Modules\Purchase\Entities\PurchaseVendor;
use Modules\Purchase\Entities\PurchaseVendorHistory;

class PurchaseVendorObserver
{

    public function creating(PurchaseVendor $vendor)
    {
        if (!isRunningInConsoleOrSeeding() && user()) {
            $vendor->added_by = user()->id;
        }

        if (company()) {
            $vendor->company_id = company()->id;
        }
    }

    public function created(PurchaseVendor $vendor)
    {
        if (! isRunningInConsoleOrSeeding()) {
            if (\user()) {
                $this->logVendorActivity(company()->id, $vendor->id, user()->id, 'vendorCreated', 'Created', null, null);
            }
        }
    }

    public function updated(PurchaseVendor $vendor)
    {
        $this->logVendorActivity(company()->id, $vendor->id, user()->id, 'vendorUpdated', 'Updated', null, null);
    }

    public function saving(PurchaseVendor $vendor)
    {
        if (!isRunningInConsoleOrSeeding() && user()) {
            $vendor->last_updated_by = user()->id;
        }
    }

    public function logVendorActivity($companyID, $vendorID, $userID, $text, $label, $vendorNotesID = null, $vendorContactID = null )
    {
        $activiy = new PurchaseVendorHistory();

        $activiy->company_id = $companyID;
        $activiy->purchase_vendor_id = $vendorID;
        $activiy->user_id = $userID;
        $activiy->details = $text;
        $activiy->label = $label;

        if (!is_null($vendorNotesID))
        {
            $activiy->purchase_vendor_notes_id = $vendorNotesID;
        }

        if (!is_null($vendorContactID))
        {
            $activiy->purchase_vendor_contact_id = $vendorContactID;
        }

        $activiy->save();
    }

}
