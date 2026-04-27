<?php

namespace Modules\Purchase\Observers;

use Modules\Purchase\Entities\PurchaseVendorContact;
use Modules\Purchase\Entities\PurchaseVendorHistory;
use Modules\Purchase\Http\Controllers\PurchaseVendorController;

class PurchaseVendorContactObserver
{

    public function creating(PurchaseVendorContact $model)
    {
        if (company()) {
            $model->company_id = company()->id;
        }
    }

    public function created(PurchaseVendorContact $event)
    {
        if (!isRunningInConsoleOrSeeding()) {
            if (\user()) {
                $this->logVendorActivity(company()->id, $event->purchase_vendor_id, user()->id, 'vendorContactCreated', 'contactCreated', null, $event->id);
            }
        }
    }

    public function updated(PurchaseVendorContact $event)
    {
        if (!isRunningInConsoleOrSeeding()) {
            if (\user()) {
                $this->logVendorActivity(company()->id, $event->purchase_vendor_id, user()->id, 'vendorContactUpdated', 'contactUpdated', null, $event->id);
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
