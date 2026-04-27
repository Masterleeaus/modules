<?php

namespace Modules\Purchase\Http\Controllers;

use App\Models\User;
use App\Helper\Reply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Support\Renderable;
use App\Http\Controllers\AccountBaseController;
use Modules\Purchase\Entities\PurchaseSetting;
use Modules\Purchase\Entities\PurchaseVendorNote;
use Modules\Purchase\Http\Requests\Vendor\StoreVendorNote;
use Modules\Purchase\DataTables\VendorNotesDataTable;
use Modules\Purchase\Entities\PurchaseVendorUserNotes;

class VendorNotesController extends AccountBaseController
{

    /**
     * Display a listing of the resource.
     * @return Renderable
     */

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.notes';

        $this->middleware(function ($request, $next) {
            abort_403(!in_array(PurchaseSetting::MODULE_NAME, $this->user->modules));

            return $next($request);
        });
    }

    public function index(VendorNotesDataTable $dataTable)
    {

        return $dataTable->render('purchase::vendors.notes.index');

    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */

    public function create()
    {
        $this->pageTitle = __('purchase::app.addVendorNote');
        $this->vendorId = request('vendor');

        $this->employees = User::allEmployees();

        if (request()->ajax()) {
            $html = view('purchase::vendors.notes.create', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'purchase::vendors.notes.create';

        return view('purchase::vendors.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */

    public function store(StoreVendorNote $request)
    {
        $this->employees = User::allEmployees();

        $note = new PurchaseVendorNote();
        $note->note_title = $request->title;
        $note->purchase_vendor_id = $request->vendor_id;
        $note->note_details = ($request->details == '<p><br></p>') ? null : $request->details;
        $note->note_type = $request->type;

        $note->ask_password = $request->ask_password ? $request->ask_password : '';

        $note->save();

        /* if note type is private */
        if ($request->type == 1) {
            $users = $request->user_id;

            if (!is_null($users)) {
                foreach ($users as $user) {
                    PurchaseVendorUserNotes::firstOrCreate([
                        'user_id' => $user,
                        'vendor_note_id' => $note->id
                    ]);
                }
            }
        }

        return Reply::successWithData(__('messages.recordSaved'), ['redirectUrl' => route('vendors.show', $note->purchase_vendor_id) . '?tab=notes']);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {

        $this->note = PurchaseVendorNote::findOrFail($id);

        $this->noteMembers = $this->note->members->pluck('user_id')->toArray();
        $this->employees = User::whereIn('id', $this->noteMembers)->get();

        $this->pageTitle = __('purchase::app.vendor') . ' ' . __('app.note');

        if (request()->ajax()) {
            $html = view('purchase::vendors.notes.show', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'purchase::vendors.notes.show';

        return view('purchase::vendors.create', $this->data);

    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $this->pageTitle = __('purchase::app.editVendorNote');

        $this->note = PurchaseVendorNote::findOrFail($id);

        $this->employees = User::allEmployees();

        $this->noteMembers = $this->note->members->pluck('user_id')->toArray();
        $this->vendorId = $this->note->purchase_vendor_id;

        if (request()->ajax()) {
            $html = view('purchase::vendors.notes.edit', $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'purchase::vendors.notes.edit';

        return view('purchase::vendors.create', $this->data);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(StoreVendorNote $request, $id)
    {
        $note = PurchaseVendorNote::findOrFail($id);
        $note->note_title = $request->title;
        $note->note_details = ($request->note_details == '<p><br></p>') ? null : $request->note_details;
        $note->note_type = $request->type;

        $note->ask_password = $request->ask_password ?: '';
        $note->save();

        /* if note type is private */
        if ($request->type == 1) {
            // delete all data of this client_note_id from client_user_notes
            PurchaseVendorUserNotes::where('vendor_note_id', $note->id)->delete();

            $users = $request->user_id;

            if (!is_null($users)) {
                foreach ($users as $user) {
                    PurchaseVendorUserNotes::firstOrCreate([
                        'user_id' => $user,
                        'vendor_note_id' => $note->id
                    ]);
                }
            }
        }

        return Reply::successWithData(__('messages.updateSuccess'), ['redirectUrl' => route('vendors.show', $note->purchase_vendor_id) . '?tab=notes']);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $this->contact = PurchaseVendorNote::findOrFail($id);

        $this->contact->delete();

        return Reply::success(__('messages.deleteSuccess'));
    }

    public function applyQuickAction(Request $request)
    {
        if ($request->action_type == 'delete') {
            $this->deleteRecords($request);

            return Reply::success(__('messages.deleteSuccess'));
        }

        return Reply::error(__('messages.selectAction'));
    }

    protected function deleteRecords($request)
    {

        PurchaseVendorNote::whereIn('id', explode(',', $request->row_ids))->delete();

        return true;
    }

    public function askForPassword($id)
    {
        $this->note = PurchaseVendorNote::findOrFail($id);

        return view('purchase::vendors.notes.verify-password', $this->data);
    }

    public function checkPassword(Request $request)
    {
        $this->client = User::findOrFail($this->user->id);

        if (Hash::check($request->password, $this->client->password)) {
            return Reply::success(__('messages.passwordMatched'));
        }

        return Reply::error(__('messages.incorrectPassword'));
    }

    public function showVerified($id)
    {
        return $this->show($id);
    }

}
