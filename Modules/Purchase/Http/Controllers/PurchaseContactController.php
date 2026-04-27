<?php

namespace Modules\Purchase\Http\Controllers;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Modules\Purchase\Entities\PurchaseSetting;
use Modules\Purchase\Entities\PurchaseVendorContact;
use Modules\Purchase\Http\Requests\VendorContact\StoreVendorContact;
use Modules\Purchase\Http\Requests\VendorContact\UpdateVendorContact;

class PurchaseContactController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();

        $this->pageTitle = 'purchase::modules.vendor.contacts';

        $this->middleware(function ($request, $next) {
            abort_403(!in_array(PurchaseSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */

    public function create()
    {
        $this->pageTitle = __('app.addContact');
        $this->managePermission = user()->permission('manage_vendor_contact');

        abort_403($this->managePermission == 'none');

        $this->vendorId = request('vendor');

        if (request()->ajax()) {
            $html = view('purchase::vendors.contacts.create', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'purchase::vendors.contacts.create';

        return view('purchase::vendors.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * @param StoreVendorContact $request
     * @return Renderable
     */
    public function store(StoreVendorContact $request)
    {
        $contact = new PurchaseVendorContact();
        $contact->purchase_vendor_id = $request->purchase_vendor_id;
        $contact->title = $request->title;
        $contact->contact_name = $request->contact_name;
        $contact->email = $request->email;
        $contact->phone = $request->phone;
        $contact->save();

        return Reply::successWithData(__('messages.recordSaved'), ['redirectUrl' => route('vendors.show', $contact->purchase_vendor_id) . '?tab=contacts']);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $this->pageTitle = __('app.editContact');
        $this->contact = PurchaseVendorContact::findOrFail($id);

        $this->vendorId = $this->contact->purchase_vendor_id;

        $this->managePermission = user()->permission('manage_vendor_contact');

        abort_403($this->managePermission == 'none');

        if (request()->ajax()) {
            $html = view('purchase::vendors.contacts.edit', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'purchase::vendors.contacts.edit';

        return view('purchase::vendors.create', $this->data);
    }

    /**
     * Update the specified resource in storage.
     * @param UpdateVendorContact $request
     * @param int $id
     * @return Renderable
     */
    public function update(UpdateVendorContact $request, $id)
    {
        $contact = PurchaseVendorContact::findOrFail($id);
        $contact->title = $request->title;
        $contact->contact_name = $request->contact_name;
        $contact->email = $request->email;
        $contact->phone = $request->phone;
        $contact->save();

        return Reply::successWithData(__('messages.updateSuccess'), ['redirectUrl' => route('vendors.show', $contact->purchase_vendor_id) . '?tab=contacts']);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $this->contact = PurchaseVendorContact::findOrFail($id);
        $this->managePermission = user()->permission('manage_vendor_contact');

        abort_403($this->managePermission == 'none');

        $this->contact->delete();

        return Reply::success(__('messages.deleteSuccess'));
    }

    public function applyQuickAction(Request $request)
    {
        switch ($request->action_type) {
        case 'delete':
            $this->deleteRecords($request);

            return Reply::success(__('messages.deleteSuccess'));
        default:
            return Reply::error(__('messages.selectAction'));
        }
    }

    protected function deleteRecords($request)
    {
        abort_403(user()->permission('delete_clients') !== 'all');
        PurchaseVendorContact::whereIn('id', explode(',', $request->row_ids))->delete();

        return true;
    }

}
