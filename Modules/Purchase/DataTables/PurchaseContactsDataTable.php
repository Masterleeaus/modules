<?php

namespace Modules\Purchase\DataTables;

use App\DataTables\BaseDataTable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Modules\Purchase\Entities\PurchaseVendorContact;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class PurchaseContactsDataTable extends BaseDataTable
{
    private $managePermission;

    public function __construct()
    {
        parent::__construct();
        $this->managePermission = user()->permission('manage_vendor_contact');
    }

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="select-table-row" id="datatable-row-' . $row->id . '"  name="datatable_ids[]" value="' . $row->id . '" onclick="dataTableRowCheck(' . $row->id . ')">';
            })
            ->addColumn('action', function ($row) {

                $action = '<div class="task_view">

                    <div class="dropdown">
                        <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link"
                            id="dropdownMenuLink-' . $row->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="icon-options-vertical icons"></i>
                        </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-' . $row->id . '" tabindex="0">';

                if ($this->managePermission == 'all') {
                    $action .= '<a class="dropdown-item openRightModal" href="' . route('purchase-contacts.edit', [$row->id]) . '">
                                <i class="fa fa-edit mr-2"></i>
                                ' . trans('app.edit') . '
                            </a>';
                }

                if ($this->managePermission == 'all') {
                    $action .= '<a class="dropdown-item delete-table-row" href="javascript:;" data-user-id="' . $row->id . '">
                                <i class="fa fa-trash mr-2"></i>
                                ' . trans('app.delete') . '
                            </a>';
                }

                $action .= '</div>
                    </div>
                </div>';

                return $action;
            })
            ->editColumn(
                'name',
                function ($row) {
                    return ($row->name);
                }
            )
            ->editColumn(
                'title',function ($row) {
                    if($row->title != null){
                        return ($row->title);
                    }
                    return '--';
            })
            ->editColumn(
                'phone',function ($row) {
                    if($row->phone != null){
                        return ($row->phone);
                    }
                    return '--';
            })
            ->editColumn(
                'created_at',
                function ($row) {
                    return Carbon::parse($row->created_at)->translatedFormat($this->company->date_format);
                }
            )
            ->addIndexColumn()
            ->smart(false)
            ->setRowId(fn($row) => 'row-' . $row->id)
            ->rawColumns(['name', 'action', 'check']);
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(PurchaseVendorContact $model)
    {
        $request = $this->request();

        return $model->where('purchase_vendor_id', $request->vendorID);
    }

    /**
     * Optional method if you want to use the html builder.
     */

    public function html(): HtmlBuilder
    {
        return $this->setBuilder('purchasecontacts-table')
            ->parameters([
                'initComplete' => 'function () {
                   window.LaravelDataTables["purchasecontacts-table"].buttons().container()
                    .appendTo( "#table-actions")
                }',
                'fnDrawCallback' => 'function( oSettings ) {
                  //
                }',
            ])
            ->buttons(Button::make(['extend' => 'excel', 'text' => '<i class="fa fa-file-export"></i> ' . trans('app.exportExcel')]));
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            'check' => [
                'title' => '<input type="checkbox" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
                'exportable' => false,
                'orderable' => false,
                'searchable' => false
            ],
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'visible' => false, 'title' => '#'],
            __('app.title') => ['data' => 'title', 'name' => 'title', 'title' => __('app.title')],
            __('app.name') => ['data' => 'contact_name', 'name' => 'contact_name', 'title' => __('app.name')],
            __('app.email') => ['data' => 'email', 'name' => 'email', 'title' => __('app.email')],
            __('app.phone') => ['data' => 'phone', 'name' => 'phone', 'title' => __('app.phone')],
            __('app.createdAt') => ['data' => 'created_at', 'name' => 'created_at', 'title' => __('app.createdAt')],
            Column::computed('action', __('app.action'))
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->addClass('text-right pr-20')
        ];
    }

    /**
     * Get the filename for export.
     */
        protected function filename(): string
        {
            return 'PurchaseContacts_' . date('YmdHis');
        }

}
