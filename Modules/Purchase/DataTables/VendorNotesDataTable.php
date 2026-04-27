<?php

namespace Modules\Purchase\DataTables;

use Carbon\Carbon;
use App\Models\User;
use App\DataTables\BaseDataTable;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Modules\Purchase\Entities\PurchaseVendor;
use Modules\Purchase\Entities\PurchaseVendorNote;

class VendorNotesDataTable extends BaseDataTable
{

    private $editClientNotePermission;
    private $deleteClientNotePermission;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {

        return datatables()
            ->eloquent($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="select-table-row" id="datatable-row-' . $row->id . '"  name="datatable_ids[]" value="' . $row->id . '" onclick="dataTableRowCheck(' . $row->id . ')">';
            })
            ->addColumn('action', function ($row) {

                $action = '<div class="task_view">';

                $action .= '<div class="dropdown">
                        <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link"
                            id="dropdownMenuLink-' . $row->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="icon-options-vertical icons"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-' . $row->id . '" tabindex="0">';

                if ($row->ask_password == 1) {
                    $action .= '<a href="javascript:;" class="dropdown-item ask-for-password" data-client-note-id="' . $row->id . '"><i class="fa fa-eye mr-2"></i>' . __('app.view') . '</a>';
                }
                else {
                    $action .= '<a href="' . route('vendor-notes.show', $row->id) . '" class="openRightModal dropdown-item"><i class="fa fa-eye mr-2"></i>' . __('app.view') . '</a>';
                }



                    $action .= '<a class="dropdown-item openRightModal" href="' . route('vendor-notes.edit', [$row->id]) . '">
                                <i class="fa fa-edit mr-2"></i>
                                ' . trans('app.edit') . '
                            </a>';


                    $action .= '<a class="dropdown-item delete-table-row" href="javascript:;" data-user-id="' . $row->id . '">
                                <i class="fa fa-trash mr-2"></i>
                                ' . trans('app.delete') . '
                            </a>';


                $action .= '</div>
                    </div>
                </div>';

                return $action;
            })
            ->editColumn('note_title', function ($row) {
                if ($row->ask_password == 1) {
                    return '<a href="javascript:;" class="ask-for-password" data-client-note-id="' . $row->id . '" style="color:black;">' . $row->note_title . '</a>';
                }
                else {
                    return '<a href="' . route('vendor-notes.show', $row->id) . '" class="openRightModal " style="color:black;">' . $row->note_title . '</a>';
                }

            })
            ->editColumn('note_type', function ($row) {
                if ($row->note_type == '0') {
                    return '<span class="badge badge-secondary"><i class="fa fa-globe"></i> ' . __('app.public') . '</span>';
                }
                else {
                    return '<span class="badge badge-primary"><i class="fa fa-lock"></i> ' . __('app.private') . '</span>';
                }
            })
            ->editColumn('id', function ($row) {
                return $row->id;
            })
            ->addIndexColumn()
            ->smart(false)
            ->setRowId(fn($row) => 'row-' . $row->id)
            ->rawColumns(['action', 'check', 'note_title', 'note_type']);
    }

    /**
     * @param ClientNote $model
     * @return ClientNote|\Illuminate\Database\Eloquent\Builder
     */

    public function query(PurchaseVendorNote $model)
    {
        $request = $this->request();

        $notes = $model->where('purchase_vendor_id', $request->vendorID);

        $notes->leftJoin('purchase_vendor_user_notes', 'purchase_vendor_user_notes.vendor_note_id', '=', 'purchase_vendor_notes.id');

        $notes->select('purchase_vendor_notes.*')->groupBy('purchase_vendor_notes.id');

        return $notes;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */

    public function html()
    {
        return $this->setBuilder('vendor-notes-table', 2)
            ->parameters([
                'initComplete' => 'function () {
                   window.LaravelDataTables["vendor-notes-table"].buttons().container()
                    .appendTo("#table-actions")
                }',
                'fnDrawCallback' => 'function( oSettings ) {
                  //
                  $(".select-picker").selectpicker();
                }',
            ])
            ->buttons(Button::make(['extend' => 'excel', 'text' => '<i class="fa fa-file-export"></i> ' . trans('app.exportExcel')]));
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            'check' => [
                'title' => '<input type="checkbox" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
                'exportable' => false,
                'orderable' => false,
                'searchable' => false
            ],
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'visible' => false, 'title' => '#'],
            __('app.id') => ['data' => 'id', 'name' => 'id', 'visible' => false, 'title' => __('app.id')],
            __('purchase::modules.vendorPayment.noteTitle') => ['data' => 'note_title', 'name' => 'note_title', 'title' => __('purchase::modules.vendorPayment.noteTitle')],
            __('purchase::modules.vendorPayment.noteType') => ['data' => 'note_type', 'name' => 'note_type', 'title' => __('purchase::modules.vendorPayment.noteType')],
            Column::computed('action', __('app.action'))
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->addClass('text-right pr-20')
        ];
    }

}
