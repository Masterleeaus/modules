<?php

namespace Modules\Accountings\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Helper\Reply;
use Modules\Accountings\Entities\Journal;
use Modules\Accountings\Entities\Journald;
use Modules\Accountings\Entities\Accounting;
use Modules\Accountings\Entities\JournalType;
use App\Http\Controllers\AccountBaseController;
use Modules\Accountings\DataTables\JournalDataTable;
use Modules\Accountings\Http\Requests\StoreJournal;

class JournalController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'accountings::modules.acc.journals';
        $this->middleware(function ($request, $next) {
            abort_403(!in_array('accountings', $this->user->modules));
            return $next($request);
        });
    }

    public function index(JournalDataTable $dataTable)
    {
        $viewPermission = user()->permission('view_acc');
        abort_403(!in_array($viewPermission, ['all']));

        $this->acc_coa    = Journal::all();
        $this->type       = JournalType::all();
        $this->totalUnits = count($this->acc_coa);
        return $dataTable->render('accountings::journal.index', $this->data);
    }

    public function create()
    {
        $this->addPermission = user()->permission('add_acc');
        abort_403(!in_array($this->addPermission, ['all']));

        $this->pageTitle   = __('accountings::app.acc.addJournal');
        $this->lastInvoice = Journal::lastInvoiceNumber() + 1;
        $this->jt          = JournalType::all();
        $this->coa         = Accounting::all();

        if (request()->ajax()) {
            $html = view('accountings::journal.ajax.create', $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'accountings::journal.ajax.create';
        return view('accountings::journal.create', $this->data);
    }

    public function store(StoreJournal $request)
    {
        $items = $request->item_name;
        if (empty($items)) {
            return Reply::error(__('accountings::messages.addItem'));
        }

        $redirectUrl             = route('journal.index');
        $journal                 = new Journal();
        $journal->journal_date   = Carbon::createFromFormat($this->company->date_format, $request->issue_date)->format('Y-m-d');
        $journal->no_journal     = $request->typejournal_id . '-' . $request->invoice_number;
        $journal->reff_journal   = $request->reff_journal;
        $journal->remark         = $request->remark;
        $journal->typejournal_id = $request->typejournal_id;
        $journal->save();

        $this->logSearchEntry($journal->id, $journal->no_journal, 'journal.show', 'journal');
        return Reply::successWithData(__('accountings::messages.addJournal'), ['redirectUrl' => $redirectUrl]);
    }

    public function destroy($id)
    {
        $firstInvoice = Journal::orderBy('id', 'desc')->first();

        if ($firstInvoice->id == $id) {
            Journal::destroy($id);
            return Reply::success(__('accountings::messages.deleteJournal'));
        } else {
            return Reply::error(__('accountings::messages.failJournal'));
        }
    }

    public function download($id)
    {
        $this->invoice = Journal::with('items.coa')->findOrFail($id)->withCustomFields();
        $pdfOption     = $this->domPdfObjectForDownload($id);
        $pdf           = $pdfOption['pdf'];
        $filename      = $pdfOption['fileName'];
        return request()->view ? $pdf->stream($filename . '.pdf'): $pdf->download($filename . '.pdf');
    }

    public function domPdfObjectForDownload($id)
    {
        $this->invoice = Journal::with('items.coa')->findOrFail($id)->withCustomFields();
        $pdf           = app('dompdf.wrapper');

        $pdf->setOption('enable_php', true);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->loadView('accountings::journal.pdf.invoice', $this->data);
        $filename = 'Journal#' . $this->invoice->no_journal;

        return [
            'pdf'      => $pdf,
            'fileName' => $filename
        ];
    }

    public function edit($id)
    {
        $this->addPermission = user()->permission('add_acc');
        abort_403(!in_array($this->addPermission, ['all']));

        $this->invoice     = Journal::with('items')->findOrFail($id)->withCustomFields();
        $this->pageTitle   = __('accountings::app.acc.editJournal');
        $this->lastInvoice = Journal::lastInvoiceNumber() + 1;
        $this->jt          = JournalType::all();
        $this->coa         = Accounting::all();
        return view('accountings::journal.ajax.edit', $this->data);
    }

    public function update(StoreJournal $request, $id)
    {
        $redirectUrl             = route('journal.index');
        $journal                 = Journal::findOrFail($id);
        $journal->journal_date   = Carbon::createFromFormat($this->company->date_format, $request->issue_date)->format('Y-m-d');
        $journal->no_journal     = $request->invoice_number;
        $journal->reff_journal   = $request->reff_journal;
        $journal->remark         = $request->remark;
        $journal->typejournal_id = $request->typejournal_id;
        $journal->save();

        // Update detail
        if (!empty(request()->item_name) && is_array(request()->item_name)) {

            $items         = request()->item_name;
            $itemsSummary  = request()->item_summary;
            $tax           = request()->taxes;
            $cost_per_item = request()->cost_per_item;
            $item_ids      = request()->item_ids;

            // Step1 - Delete all invoice items which are not avaialable
            if (!empty($item_ids)) {
                Journald::whereNotIn('id', $item_ids)->where('journal_id', $journal->id)->delete();
            }

            // Step2&3 - Find old invoices items, update it and check if images are newer or older
            foreach ($items as $key => $item) {
                $invoice_item_id = isset($item_ids[$key]) ? $item_ids[$key] : 0;

                try {
                    $invoiceItem = Journald::findOrFail($invoice_item_id);
                } catch (Exception) {
                    $invoiceItem = new Journald();
                }

                $invoiceItem->journal_id = $journal->id;
                $invoiceItem->debit      = $item;
                $invoiceItem->credit     = $cost_per_item[$key];
                $invoiceItem->notes      = $itemsSummary[$key];
                $invoiceItem->coa_id     = $tax[$key];
                $invoiceItem->saveQuietly();
            }
        }

        return Reply::successWithData(__('accountings::messages.updateJournal'), ['redirectUrl' => $redirectUrl]);
    }

    public function show($id)
    {
        $this->addPermission = user()->permission('add_acc');
        abort_403(!in_array($this->addPermission, ['all']));

        $this->invoice     = Journal::with('items.coa')->findOrFail($id)->withCustomFields();
        $this->pageTitle   = __('accountings::app.acc.showJournal');
        $this->lastInvoice = Journal::lastInvoiceNumber() + 1;
        $this->jt          = JournalType::all();
        $this->coa         = Accounting::all();
        return view('accountings::journal.show', $this->data);
    }
}
