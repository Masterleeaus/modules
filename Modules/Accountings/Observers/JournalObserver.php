<?php
namespace Modules\Accountings\Observers;

use App\Traits\UnitTypeSaveTrait;
use Exception;
use App\Models\UniversalSearch;
use Modules\Accountings\Entities\Journal;
use Modules\Accountings\Entities\Journald;

class JournalObserver
{
    use UnitTypeSaveTrait;

    public function saving(Journal $journal)
    {
        // $this->unitType($journal);
        if (!isRunningInConsoleOrSeeding()) {
            if (company()) {
                $journal->company_id = company()->id;
            }
        }
    }

    public function creating(Journal $journal)
    {

        if (!isRunningInConsoleOrSeeding()) {

            if (request()->type && request()->type == 'draft') {
                $journal->status = 'draft';
            }
        }

        if (company()) {
            $journal->company_id = company()->id;
        }
    }

    public function created(Journal $journal)
    {
        if (!isRunningInConsoleOrSeeding()) {
            if (!empty(request()->item_name) && is_array(request()->item_name)) {

                $itemsSummary = request()->item_summary;
                $cost_per_item = request()->cost_per_item;
                $tax = request()->taxes;

                foreach (request()->item_name as $key => $item) :
                    if (!is_null($item)) {
                        $invoiceItem = Journald::create(
                            [
                                'journal_id' => $journal->id,
                                'debit' => $item,
                                'credit' => $cost_per_item[$key],
                                'notes' => $itemsSummary[$key],
                                'coa_id' => $tax[$key],
                            ]
                        );
                    }
                endforeach;
            }
        }
        $journal->saveQuietly();
    }

    // public function updated(Journal $journal)
    // {
    //     if (!isRunningInConsoleOrSeeding()) {
    //         if (!empty(request()->item_name) && is_array(request()->item_name)) {

    //             $items = request()->item_name;
    //             $itemsSummary = request()->item_summary;
    //             $tax = request()->taxes;
    //             $cost_per_item = request()->cost_per_item;
    //             $item_ids = request()->item_ids;

    //             // Step1 - Delete all invoice items which are not avaialable
    //             if (!empty($item_ids)) {
    //                 Journald::whereNotIn('id', $item_ids)->where('journal_id', $journal->id)->delete();
    //             }

    //             // Step2&3 - Find old invoices items, update it and check if images are newer or older
    //             foreach ($items as $key => $item) {
    //                 $invoice_item_id = isset($item_ids[$key]) ? $item_ids[$key] : 0;

    //                 try {
    //                     $invoiceItem = Journald::findOrFail($invoice_item_id);
    //                 }
    //                 catch(Exception )  {
    //                         $invoiceItem = new Journald();
    //                 }

    //                 $invoiceItem->journal_id = $journal->id;
    //                 $invoiceItem->debit = $item;
    //                 $invoiceItem->credit = $cost_per_item[$key];
    //                 $invoiceItem->notes = $itemsSummary[$key];
    //                 $invoiceItem->coa_id = $tax[$key];
    //                 $invoiceItem->saveQuietly();

    //             }
    //         }
    //     }
    //     $journal->saveQuietly();
    // }

    public function deleting(Journal $journal)
    {
        $universalSearches = UniversalSearch::where('searchable_id', $journal->id)->where('module_type', 'journal')->get();

        if ($universalSearches) {
            foreach ($universalSearches as $universalSearch) {
                UniversalSearch::destroy($universalSearch->id);
            }
        }

    }

}


