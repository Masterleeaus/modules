<?php

namespace Modules\Purchase\DataTables;

use App\Models\LeadFollowup;
use App\DataTables\BaseDataTable;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class PurchaseProductTransaction extends BaseDataTable
{
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
            ->addColumn('action', function ($row) {
                return '--';
            })
            ->addColumn('created_at', function ($row) {
                return '--';
            })
            ->addColumn('next_follow_up', function ($row) {
                return '--';
            })
            ->smart(false)
            ->setRowId(fn($row) => 'row-' . $row->id)

            ->rawColumns(['action', 'status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\LeadFollowup $model
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function query(LeadFollowup $model)
    {
        $lead = $model->newQuery();

        if (request()->has('leadId') && request()->leadId != '') {
            $lead = $lead->where('lead_id', request()->leadId);
        }

        return $lead;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->setBuilder('transactions-table')
            ->parameters([
            'initComplete' => 'function () {
                window.LaravelDataTables["transactions-table"].buttons().container()
                .appendTo("#table-actions")
            }',
            'fnDrawCallback' => 'function( oSettings ) {
                $("body").tooltip({
                    selector: \'[data-toggle="tooltip"]\'
                });
                $(".statusChange").selectpicker();
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
            __('modules.invoices.item') => ['data' => 'created_at', 'name' => 'created_at', 'title' => __('modules.invoices.item')],
            __('modules.invoices.unitPrice') => ['data' => 'next_follow_up', 'name' => 'next_follow_up', 'title' => __('modules.invoices.unitPrice')],
            Column::computed('action', __('app.action'))
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->addClass('text-right pr-20')
        ];

    }

}
