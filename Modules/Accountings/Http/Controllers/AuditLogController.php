<?php

namespace Modules\Accountings\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AccountBaseController;
use Modules\Accountings\Entities\AuditLog;

class AuditLogController extends AccountBaseController
{
    public function index(Request $request)
    {
        $this->pageTitle = __('accountings::app.menu.accounting') . ' - Audit';

        $query = AuditLog::query();

        if ($request->filled('action')) {
            $query->where('action', $request->get('action'));
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->get('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->get('to'));
        }

        $logs = $query->latest()->paginate(50)->withQueryString();
        $actions = AuditLog::query()->select('action')->distinct()->orderBy('action')->pluck('action');

        return view('accountings::audit.index', compact('logs', 'actions'));
    }
}
