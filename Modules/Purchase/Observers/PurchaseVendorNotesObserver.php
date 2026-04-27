<?php

namespace Modules\Purchase\Observers;

use Modules\Purchase\Entities\PurchaseVendorNote;
use Modules\Purchase\Entities\PurchaseVendorHistory;
use Modules\Purchase\Http\Controllers\PurchaseVendorController;

class PurchaseVendorNotesObserver
{

    public function creating(PurchaseVendorNote $model)
    {
        if (company()) {
            $model->company_id = company()->id;
        }
    }

    public function created(PurchaseVendorNote $event)
    {
        if (!isRunningInConsoleOrSeeding()) {
            if (\user()) {
                $this->logVendorActivity(company()->id, $event->purchase_vendor_id, user()->id, 'vendorNoteCreated', 'noteCreated', $event->id, null);
            }
        }
    }

    public function updated(PurchaseVendorNote $event)
    {
        if (!isRunningInConsoleOrSeeding()) {
            if (\user()) {
                $this->logVendorActivity(company()->id, $event->purchase_vendor_id, user()->id, 'vendorNoteUpdated', 'noteUpdated', $event->id, null);
            }
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
