<?php

namespace Modules\Accountings\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AccountBaseController;
use Modules\Accountings\Entities\PeriodLock;
use Modules\Accountings\Services\AuditService;

class PeriodLockController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Period Locks';
        $this->pageIcon  = 'ti-lock';

        $this->middleware(function ($request, $next) {
            abort_403(! in_array('accountings', $this->user->modules));
            return $next($request);
        });
    }

    public function index()
    {
        $locks = PeriodLock::where('company_id', $this->user->company_id)
            ->orderByDesc('lock_date')
            ->get();

        return view('accountings::banking.locks', compact('locks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'lock_date' => 'required|date',
            'reason'    => 'nullable|string|max:500',
        ]);

        PeriodLock::create([
            'company_id' => $this->user->company_id,
            'user_id'    => $this->user->id,
            'lock_date'  => $request->lock_date,
            'locked_by'  => $this->user->id,
            'reason'     => $request->reason,
        ]);

        AuditService::log('period_lock', 'period_lock', null, ['lock_date' => $request->lock_date]);

        return redirect()->route('accountings.period-locks.index')
            ->with('success', 'Period locked to ' . $request->lock_date . '.');
    }

    public function destroy($id)
    {
        $lock = PeriodLock::where('company_id', $this->user->company_id)->findOrFail($id);
        $lock->delete();

        AuditService::log('period_unlock', 'period_lock', $id, []);

        return redirect()->route('accountings.period-locks.index')
            ->with('success', 'Period lock removed.');
    }
}
