<?php

namespace Modules\Accountings\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AccountBaseController;
use Modules\Accountings\Entities\GstPeriod;
use Modules\Accountings\Services\GstReportService;

class GstController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'GST / BAS';
        $this->pageIcon  = 'ti-stats-up';

        $this->middleware(function ($request, $next) {
            abort_403(! in_array('accountings', $this->user->modules));
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $from  = $request->get('from');
        $to    = $request->get('to');
        $basis = $request->get('basis', 'accrual');

        $service = new GstReportService();
        $data    = $service->summary($from, $to, $basis);

        $periods = GstPeriod::where('company_id', $this->user->company_id)
            ->orderByDesc('period_start')
            ->limit(12)
            ->get();

        return view('accountings::reports.gst', array_merge($data, compact('periods')));
    }

    public function export(Request $request)
    {
        $from  = $request->get('from');
        $to    = $request->get('to');
        $basis = $request->get('basis', 'accrual');

        $service = new GstReportService();
        $data    = $service->summary($from, $to, $basis);

        $period = GstPeriod::firstOrCreate(
            [
                'company_id'   => $this->user->company_id,
                'period_start' => $from ?: now()->startOfQuarter()->toDateString(),
                'period_end'   => $to ?: now()->endOfQuarter()->toDateString(),
            ],
            [
                'user_id'       => $this->user->id,
                'period_type'   => 'quarterly',
                'gst_collected' => $data['gst_collected'] ?? 0,
                'gst_paid'      => $data['gst_paid'] ?? 0,
                'net_gst'       => $data['net_gst'] ?? 0,
                'status'        => 'draft',
            ]
        );

        $bas = $service->exportBas($period);

        $filename = 'bas_' . ($from ?? 'all') . '_' . ($to ?? 'all') . '_' . date('Ymd') . '.csv';
        $rows = [['field', 'value']];
        foreach ($bas as $key => $value) {
            $rows[] = [$key, $value];
        }

        $out = '';
        foreach ($rows as $row) {
            $out .= implode(',', array_map(fn($v) => '"' . str_replace('"', '""', (string) $v) . '"', $row)) . "\n";
        }

        return response($out, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
