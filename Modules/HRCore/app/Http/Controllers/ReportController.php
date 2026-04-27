<?php

namespace Modules\HRCore\app\Http\Controllers;

use App\Exports\AttendanceExport;
use App\Exports\ExpenseReport;
use App\Exports\LeaveReport;
use App\Exports\VisitExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Modules\ProductOrder\App\Exports\ProductOrderReport;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hrcore.view-hr-reports', ['only' => ['index']]);
        $this->middleware('permission:hrcore.generate-attendance-reports', ['only' => ['getAttendanceReport']]);
        $this->middleware('permission:hrcore.generate-leave-reports', ['only' => ['getLeaveReport']]);
        $this->middleware('permission:hrcore.generate-expense-reports', ['only' => ['getExpenseReport']]);
        $this->middleware('permission:hrcore.export-hr-reports', ['only' => ['exportAttendanceReport', 'exportLeaveReport', 'exportExpenseReport']]);
    }

    public function index()
    {
        return view('tenant.reports.index');
    }

    public function getAttendanceReport(Request $request)
    {
        $period = $request->period;

        if (! $period) {
            return redirect()->back()->with('error', 'Please select a period');
        }

        $month = date('m', strtotime($period));

        $year = date('Y', strtotime($period));

        return Excel::download(new AttendanceExport($month, $year), time().'_attendance_report.xlsx');
    }

    public function getVisitReport(Request $request)
    {
        $period = $request->period;

        if (! $period) {
            return redirect()->back()->with('error', 'Please select a period');
        }

        $month = date('m', strtotime($period));

        $year = date('Y', strtotime($period));

        return Excel::download(new VisitExport($month, $year), time().'_visit_report.xlsx');
    }

    public function getLeaveReport(Request $request)
    {
        $period = $request->period;

        if (! $period) {
            return redirect()->back()->with('error', 'Please select a period');
        }

        $month = date('m', strtotime($period));

        $year = date('Y', strtotime($period));

        return Excel::download(new LeaveReport($month, $year), time().'_leave_report.xlsx');
    }

    public function getExpenseReport(Request $request)
    {
        $period = $request->period;

        if (! $period) {
            return redirect()->back()->with('error', 'Please select a period');
        }

        $month = date('m', strtotime($period));

        $year = date('Y', strtotime($period));

        return Excel::download(new ExpenseReport($month, $year), time().'_expense_report.xlsx');
    }

    public function getProductOrderReport(Request $request)
    {
        $period = $request->period;

        if (! $period) {
            return redirect()->back()->with('error', 'Please select a period');
        }

        $month = date('m', strtotime($period));
        $year = date('Y', strtotime($period));

        return Excel::download(new ProductOrderReport($month, $year), time().'_product_order_report.xlsx');
    }
}
