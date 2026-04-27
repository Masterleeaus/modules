<?php

namespace Modules\Accountings\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AccountBaseController;
use Modules\Accountings\Services\GstReportService;
use Modules\Accountings\Entities\AccountingSetting;

class GstReportController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'GST';
        $this->pageIcon = 'ti-stats-up';

        $this->middleware(function ($request, $next) {
            abort_403(!in_array('accountings', $this->user->modules));
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $from = $request->get('from');
        $to = $request->get('to');
        $defaultBasis = (AccountingSetting::first()?->gst_basis) ?: 'accrual';
        $basis = $request->get('basis', $defaultBasis);
        $data = (new GstReportService())->summary($from, $to, $basis);
        return view('accountings::reports.gst', $data);
    }


    public function export(Request $request)
    {
        $from = $request->get('from');
        $to = $request->get('to');
        $defaultBasis = (AccountingSetting::first()?->gst_basis) ?: 'accrual';
        $basis = $request->get('basis', $defaultBasis);
        $data = (new GstReportService())->summary($from, $to, $basis);

        $filename = 'gst_summary_' . ($data['basis'] ?? 'accrual') . '_' . date('Ymd_His') . '.csv';

        $lines = [
            ['basis','from','to','gst_collected','gst_paid','gst_paid_bills','gst_paid_expenses','net_gst'],
            [
                $data['basis'] ?? '',
                $data['from'] ?? '',
                $data['to'] ?? '',
                $data['gst_collected'] ?? 0,
                $data['gst_paid'] ?? 0,
                $data['gst_paid_bills'] ?? 0,
                $data['gst_paid_expenses'] ?? 0,
                $data['net_gst'] ?? 0,
            ],
        ];

        $out = '';
        foreach ($lines as $row) {
            $out .= implode(',', array_map(function ($v) {
                $v = (string)$v;
                $v = str_replace('"', '""', $v);
                if (str_contains($v, ',') || str_contains($v, "\n")) {
                    return '"' . $v . '"';
                }
                return $v;
            }, $row)) . "\n";
        }

        return response($out, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
