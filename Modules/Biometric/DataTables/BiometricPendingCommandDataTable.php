<?php

namespace Modules\Biometric\DataTables;

use App\DataTables\BaseDataTable;
use Modules\Biometric\Entities\BiometricCommands;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class BiometricPendingCommandDataTable extends BaseDataTable
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
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return '<div class="task_view">
                    <a href="javascript:;" class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-ellipsis-v"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="javascript:;" onclick="deletePendingCommand(' . $row->id . ')">
                            <i class="fa fa-trash"></i> ' . __('app.delete') . '
                        </a>
                    </div>
                </div>';
            })
            ->addColumn('status', function ($row) {
                $statusClass = [
                    'pending' => 'bg-warning',
                    'completed' => 'bg-success',
                    'failed' => 'bg-danger'
                ];

                return '<span class="badge ' . ($statusClass[$row->status] ?? 'bg-secondary') . '">' . ucfirst($row->status) . '</span>';
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at->format(company()->date_format . ' ' . company()->time_format);
            })
            ->rawColumns(['action', 'status'])
            ->setRowId(function ($row) {
                return 'row-' . $row->id;
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \Modules\Biometric\Entities\BiometricCommands $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(BiometricCommands $model)
    {
        return $model->with(['device', 'user'])
            ->where('company_id', company()->id)
            ->orderBy('created_at', 'desc');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('pending-commands-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row'<'col-sm-12'tr>>" .
                "<'row'<'col-sm-12 col-md-5'l><'col-sm-12 col-md-7'p>>")
            ->orderBy(5, 'desc')
            ->buttons(
                Button::make(['extend' => 'excel', 'text' => '<i class="fa fa-file-export"></i> ' . __('app.exportExcel')]),
                Button::make(['extend' => 'csv', 'text' => '<i class="fa fa-file"></i> ' . __('app.exportCSV')])
            )
            ->parameters([
                'scrollX' => true,
                'drawCallback' => 'function() {
                    KTMenu.createInstances();
                }',
                'language' => [
                    'url' => url('vendor/datatables/lang/' . user()->locale . '.json')
                ],
            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::make('DT_RowIndex')
                ->title('#')
                ->width(10)
                ->addClass('text-center')
                ->content(function ($row, $column, $index) {
                    return $index + 1;
                }),
            Column::make('type')
                ->title(__('app.type'))
                ->width(20),
            Column::make('device.serial_number')
                ->title(__('app.device'))
                ->width(20),
            Column::make('user.name')
                ->title(__('app.employee'))
                ->width(20),
            Column::make('status')
                ->title(__('app.status'))
                ->width(20),
            Column::make('created_at')
                ->title(__('app.created_at'))
                ->width(20),
            Column::computed('action')
                ->title(__('app.action'))
                ->exportable(false)
                ->printable(false)
                ->width(20)
                ->addClass('text-center')
        ];
    }
}
