<?php

namespace Modules\Biometric\DataTables;

use App\DataTables\BaseDataTable;
use App\Scopes\ActiveScope;
use App\Scopes\CompanyScope;
use Modules\Biometric\Entities\BiometricEmployee;

class BiometricEmployeeDataTable extends BaseDataTable
{

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
            ->addIndexColumn()
            ->addColumn('employee_id', fn($row) => $row->user->employeeDetail->employee_id)
            ->addColumn('name', fn($row) => $row->user->name)
            ->addColumn('email', fn($row) => $row->user->email)
            ->addColumn('have_fingerprint', fn($row) => $row->have_fingerprint ? '<span class="badge badge-success">' . __('app.yes') . '</span>' : '<span class="badge badge-danger">' . __('app.no') . '</span>')
            ->addColumn('fingerprint_count', fn($row) => $row->fingerprint_count ?? 0)
            ->rawColumns(['have_fingerprint']);
    }

    /**
     * Get query source of dataTable.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(BiometricEmployee $model)
    {
        $searchText = request('searchText');

        $model = $model->with(['user', 'user.employeeDetail'])
        ->withoutGlobalScopes([ActiveScope::class, CompanyScope::class])
        ->whereHas('user', function ($query) use ($searchText) {
            $query->where('name', 'like', '%' . $searchText . '%')
                ->orWhere('email', 'like', '%' . $searchText . '%');
        });

        return $model;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->setBuilder('biometric-employee-table', 0)
            ->parameters([
                'initComplete' => 'function () {
                    window.LaravelDataTables["biometric-employee-table"].buttons().container()
                    .appendTo("#table-actions")
                }',
                'fnDrawCallback' => 'function( oSettings ) {
                    $("body").tooltip({
                        selector: \'[data-toggle="tooltip"]\'
                    });
                    $(".select-picker").selectpicker();
                }',
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
            '#' => ['data' => 'id', 'name' => 'id', 'visible' => false],
            __('modules.employees.employeeId') => ['data' => 'employee_id', 'name' => 'employee_id', 'title' => __('modules.employees.employeeId')],
            __('app.name') => ['data' => 'name', 'name' => 'name', 'exportable' => false, 'title' => __('app.name')],
            __('app.email') => ['data' => 'email', 'name' => 'email', 'title' => __('app.email')],
            __('biometric::app.haveFingerPrint') => ['data' => 'have_fingerprint', 'name' => 'have_fingerprint', 'title' => __('biometric::app.haveFingerPrint')],
            __('biometric::app.fingerCount') => ['data' => 'fingerprint_count', 'name' => 'fingerprint_count', 'title' => __('biometric::app.fingerCount')],
        ];
    }

}

