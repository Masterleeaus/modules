<?php

namespace Modules\Biometric\DataTables;

use App\DataTables\BaseDataTable;
use App\Scopes\ActiveScope;
use App\Scopes\CompanyScope;
use Modules\Biometric\Entities\BiometricAttendance;
use Illuminate\Support\Facades\DB;

class BiometricAttendanceDataTable extends BaseDataTable
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
            ->addColumn('employee_id', function ($row) {
                if (!$row->user) {
                    return '<span class="badge badge-secondary">' . $row->employee_id . '</span>';
                }

                return
                    '<div class="d-flex align-items-center">
                                <span class="px-2 py-1 rounded mr-2">
                                    <span>' . $row->employee_id . '</span>
                                </span>
                            ' . view('components.employee', ['user' => $row->user]) . '
                        </div>';
            })
            ->addColumn('device_name', function ($row) {
                return $row->device_name;
            })
            ->addColumn('status1', function ($row) {
                if ($row->status1 == 1) {
                    return '<span class="badge badge-danger"><i class="fas fa-arrow-right"></i> ' . __('modules.attendance.clock_out') . '</span>';
                }

                return '<span class="badge badge-success"><i class="fas fa-arrow-left"></i> ' . __('modules.attendance.clock_in') . '</span>';
            })
            ->rawColumns(['employee_id', 'device_name', 'status1'])
            ->addColumn('status2', function ($row) {
                if ($row->status2 == 15) return 'Face';
                if ($row->status2 == 25) return 'Palm';
                else return $row->status2;
            })
            ->addColumn('timestamp', fn($row) => $row->timestamp->setTimezone(company()->timezone)->format(company()->date_format . ' ' . company()->time_format))
            ->rawColumns(['employee_id', 'device_name', 'status1', 'status2']);
    }

    /**
     * Get query source of dataTable.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(BiometricAttendance $model)
    {
        $searchText = request('searchText');
        $userId = request('user_id');
        $month = request('month');
        $year = request('year');
        $startDate = request('startDate');
        $endDate = request('endDate');
        if ($startDate !== null && $startDate != 'null' && $startDate != '') {
            $startDate = companyToDateString($startDate);
            $model = $model->where(DB::raw('DATE(timestamp)'), '>=', $startDate);
        }

        if ($endDate !== null && $endDate != 'null' && $endDate != '') {
            $endDate = companyToDateString($endDate);
            $model = $model->where(DB::raw('DATE(timestamp)'), '<=', $endDate);
        }

        if ($searchText != '') {
            $model = $model->where(function ($query) use ($searchText) {
                $query->where('device_name', 'like', '%' . $searchText . '%')
                    ->orWhere('device_serial_number', 'like', '%' . $searchText . '%')
                    ->orWhere('status1', 'like', '%' . $searchText . '%')
                    ->orWhere('employee_id', 'like', '%' . $searchText . '%')
                    ->orWhere('timestamp', 'like', '%' . $searchText . '%');
            });
        }

        if ($userId !== 'all') {
            $model = $model->where('user_id', $userId);
        }

        if ($month !== 'all') {
            $model = $model->where('timestamp', 'like', '%' . $month . '%');
        }

        if ($year !== 'all') {
            $model = $model->where('timestamp', 'like', '%' . $year . '%');
        }




        return $model;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->setBuilder('biometric-attendance-table', 0)
            ->parameters([
                'initComplete' => 'function () {
                    window.LaravelDataTables["biometric-attendance-table"].buttons().container()
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
            __('biometric::app.deviceName') => ['data' => 'device_name', 'name' => 'device_name', 'title' => __('biometric::app.deviceName')],

            __('biometric::app.status1') => ['data' => 'status1', 'name' => 'status1', 'title' => __('app.status')],
            __('biometric::app.timestamp') => ['data' => 'timestamp', 'name' => 'timestamp', 'title' => __('biometric::app.timestamp')],

        ];
    }
}
