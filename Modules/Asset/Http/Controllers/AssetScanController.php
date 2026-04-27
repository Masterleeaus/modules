<?php

namespace Modules\Asset\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Http\Controllers\AccountBaseController;
use Modules\Asset\Entities\Asset;
use Modules\Asset\Entities\AssetSetting;

class AssetScanController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();

        $this->middleware(function ($request, $next) {
            abort_403(!in_array(AssetSetting::MODULE_NAME, $this->user->modules));
            $this->pageTitle = __('asset::app.menu.asset');

            return $next($request);
        });
    }

    /**
     * Mobile-first scan landing page.
     * QR codes should point here.
     */
    public function show($asset)
    {
        $viewPermission = user()->permission('view_asset');
        abort_403($viewPermission == 'none');

        $this->asset = Asset::with(['assetType', 'latestHistory'])->findOrFail($asset);

        return view('asset::asset.scan', $this->data);
    }


/**
 * Issue asset (quick action from scan page).
 * Expects optional: issued_to_user_id, issued_to_job_id, note
 */
public function issue(Request $request, Asset $asset)
{
    $data = $request->validate([
        'issued_to_user_id' => ['nullable', 'integer'],
        'issued_to_job_id'  => ['nullable', 'integer'],
        'note'              => ['nullable', 'string', 'max:1000'],
    ]);

    // Update asset status (don't assume enum; keep string)
    if (Schema::hasColumn('assets', 'status')) {
        $asset->status = 'in_use';
    }
    if (Schema::hasColumn('assets', 'location') && !empty($data['issued_to_job_id'])) {
        // Leave location update for job integration pass
    }
    $asset->save();

    // Write history if available
    if (class_exists(AssetHistory::class)) {
        AssetHistory::create([
            'asset_id' => $asset->id,
            'type' => 'issue',
            'details' => json_encode([
                'issued_to_user_id' => $data['issued_to_user_id'] ?? null,
                'issued_to_job_id'  => $data['issued_to_job_id'] ?? null,
                'note'              => $data['note'] ?? null,
            ]),
            'created_by' => auth()->id(),
            'company_id' => (Schema::hasColumn('asset_histories', 'company_id') ? (auth()->user()->company_id ?? null) : null),
        ]);
    }

    return back()->with('success', 'Equipment issued.');
}

/**
 * Return asset (quick action from scan page).
 * Expects optional: location, note
 */
public function returnAsset(Request $request, Asset $asset)
{
    $data = $request->validate([
        'location' => ['nullable', 'string', 'max:191'],
        'note'     => ['nullable', 'string', 'max:1000'],
    ]);

    if (Schema::hasColumn('assets', 'status')) {
        $asset->status = 'available';
    }
    if (Schema::hasColumn('assets', 'location') && !empty($data['location'])) {
        $asset->location = $data['location'];
    }
    $asset->save();

    if (class_exists(AssetHistory::class)) {
        AssetHistory::create([
            'asset_id' => $asset->id,
            'type' => 'return',
            'details' => json_encode([
                'location' => $data['location'] ?? null,
                'note'     => $data['note'] ?? null,
            ]),
            'created_by' => auth()->id(),
            'company_id' => (Schema::hasColumn('asset_histories', 'company_id') ? (auth()->user()->company_id ?? null) : null),
        ]);
    }

    return back()->with('success', 'Equipment returned.');
}

/**
 * Report damage/missing (quick action).
 * Expects: status (damaged|lost|non_functional), note
 */
public function report(Request $request, Asset $asset)
{
    $data = $request->validate([
        'status' => ['required', 'string', 'max:50'],
        'note'   => ['nullable', 'string', 'max:2000'],
    ]);

    if (Schema::hasColumn('assets', 'status')) {
        $asset->status = $data['status'];
        $asset->save();
    }

    if (class_exists(AssetHistory::class)) {
        AssetHistory::create([
            'asset_id' => $asset->id,
            'type' => 'report',
            'details' => json_encode([
                'status' => $data['status'],
                'note'   => $data['note'] ?? null,
            ]),
            'created_by' => auth()->id(),
            'company_id' => (Schema::hasColumn('asset_histories', 'company_id') ? (auth()->user()->company_id ?? null) : null),
        ]);
    }

    return back()->with('success', 'Report saved.');
}

/**
 * Create a maintenance record and mark asset under maintenance.
 */
public function sendToMaintenance(Request $request, Asset $asset)
{
    $data = $request->validate([
        'reason' => ['nullable', 'string', 'max:1000'],
        'due_at' => ['nullable', 'date'],
    ]);

    if (class_exists(AssetMaintenance::class)) {
        $payload = [
            'asset_id' => $asset->id,
            'status'   => 'open',
            'reason'   => $data['reason'] ?? 'Sent to maintenance from scan.',
            'due_at'   => !empty($data['due_at']) ? Carbon::parse($data['due_at']) : null,
        ];
        if (Schema::hasColumn('asset_maintenances', 'company_id')) {
            $payload['company_id'] = auth()->user()->company_id ?? null;
        }
        if (Schema::hasColumn('asset_maintenances', 'created_by')) {
            $payload['created_by'] = auth()->id();
        }
        AssetMaintenance::create($payload);
    }

    if (Schema::hasColumn('assets', 'status')) {
        $asset->status = 'under_maintenance';
        $asset->save();
    }

    return back()->with('success', 'Sent to maintenance.');
}

/**
 * Complete latest open maintenance record and mark asset available.
 */
public function completeMaintenance(Request $request, Asset $asset)
{
    $data = $request->validate([
        'note' => ['nullable', 'string', 'max:2000'],
    ]);

    if (class_exists(AssetMaintenance::class)) {
        $q = AssetMaintenance::where('asset_id', $asset->id)
            ->where('status', 'open')
            ->orderByDesc('id');

        if (Schema::hasColumn('asset_maintenances', 'company_id') && auth()->check() && isset(auth()->user()->company_id)) {
            $q->where(function($qq){
                // best-effort tenant scope
            });
        }

        $maint = $q->first();
        if ($maint) {
            $maint->status = 'completed';
            if (Schema::hasColumn('asset_maintenances', 'completed_at')) {
                $maint->completed_at = Carbon::now();
            }
            if (Schema::hasColumn('asset_maintenances', 'notes')) {
                $maint->notes = trim(($maint->notes ?? '')."\n".$data['note']);
            }
            $maint->save();
        }
    }

    if (Schema::hasColumn('assets', 'status')) {
        $asset->status = 'available';
        $asset->save();
    }

    return back()->with('success', 'Maintenance completed.');
}

/**
 * Allocate asset (creates a transaction record).
 */
public function allocate(Request $request, Asset $asset)
{
    $data = $request->validate([
        'allocated_to_user_id' => ['nullable', 'integer'],
        'note'                 => ['nullable', 'string', 'max:1000'],
    ]);

    if (class_exists(AssetTransaction::class)) {
        $payload = [
            'asset_id' => $asset->id,
            'type'     => 'allocate',
            'details'  => json_encode([
                'allocated_to_user_id' => $data['allocated_to_user_id'] ?? null,
                'note' => $data['note'] ?? null,
            ]),
        ];
        if (Schema::hasColumn('asset_transactions', 'company_id')) {
            $payload['company_id'] = auth()->user()->company_id ?? null;
        }
        if (Schema::hasColumn('asset_transactions', 'created_by')) {
            $payload['created_by'] = auth()->id();
        }
        AssetTransaction::create($payload);
    }

    if (Schema::hasColumn('assets', 'status')) {
        $asset->status = 'allocated';
        $asset->save();
    }

    return back()->with('success', 'Equipment allocated.');
}

/**
 * Revoke allocation (writes a transaction + sets available).
 */
public function revokeAllocation(Request $request, Asset $asset)
{
    $data = $request->validate([
        'note' => ['nullable', 'string', 'max:1000'],
    ]);

    if (class_exists(AssetTransaction::class)) {
        $payload = [
            'asset_id' => $asset->id,
            'type'     => 'revoke_allocation',
            'details'  => json_encode([
                'note' => $data['note'] ?? null,
            ]),
        ];
        if (Schema::hasColumn('asset_transactions', 'company_id')) {
            $payload['company_id'] = auth()->user()->company_id ?? null;
        }
        if (Schema::hasColumn('asset_transactions', 'created_by')) {
            $payload['created_by'] = auth()->id();
        }
        AssetTransaction::create($payload);
    }

    if (Schema::hasColumn('assets', 'status')) {
        $asset->status = 'available';
        $asset->save();
    }

    return back()->with('success', 'Allocation revoked.');
}

}
