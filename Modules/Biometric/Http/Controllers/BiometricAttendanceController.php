<?php

namespace Modules\Biometric\Http\Controllers;

use App\Http\Controllers\AccountBaseController;
use App\Models\User;
use Modules\Biometric\DataTables\BiometricAttendanceDataTable;


class BiometricAttendanceController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'biometric::app.menu.deviceEmployees';

        $this->middleware(function ($request, $next) {
            abort_403(!in_array('biometric', $this->user->modules) && user()->permission('manage_biometric_settings') != 'none');
            return $next($request);
        });
    }



    /**
     * Display a listing of the resource.
     */
    public function index(BiometricAttendanceDataTable $dataTable)
    {
        $this->pageTitle = 'biometric::app.menu.attendance';
        $this->employees = User::allEmployees();
        $this->viewAttendancePermission = user()->permission('view_attendance');

        $now = now();
        $this->year = $now->format('Y');
        $this->month = $now->format('m');

        return $dataTable->render('biometric::attendance.index', $this->data);
    }
}
