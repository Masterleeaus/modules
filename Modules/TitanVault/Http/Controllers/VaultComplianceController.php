<?php

namespace Modules\TitanVault\Http\Controllers;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use Illuminate\Http\Request;
use Modules\TitanVault\Entities\VaultComplianceDocument;

class VaultComplianceController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Compliance Documents';
    }

    /**
     * List all compliance documents for the current company with expiry status badges.
     */
    public function index()
    {
        abort_403(!$this->user->permission('manage_compliance_docs'));

        $companyId = $this->user->company_id;

        $this->expired      = VaultComplianceDocument::with('document', 'staff')
            ->where('company_id', $companyId)
            ->expired()
            ->orderBy('expiry_date')
            ->get();

        $this->expiringSoon = VaultComplianceDocument::with('document', 'staff')
            ->where('company_id', $companyId)
            ->expiringSoon(30)
            ->orderBy('expiry_date')
            ->get();

        $this->active = VaultComplianceDocument::with('document', 'staff')
            ->where('company_id', $companyId)
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '>', now()->addDays(30)->toDateString())
            ->orderBy('expiry_date')
            ->get();

        $this->noExpiry = VaultComplianceDocument::with('document', 'staff')
            ->where('company_id', $companyId)
            ->whereNull('expiry_date')
            ->orderByDesc('created_at')
            ->get();

        $this->complianceTypes = [
            VaultComplianceDocument::TYPE_INSURANCE,
            VaultComplianceDocument::TYPE_POLICE_CHECK,
            VaultComplianceDocument::TYPE_WWCC,
            VaultComplianceDocument::TYPE_SDS,
            VaultComplianceDocument::TYPE_OTHER,
        ];

        return view('titan_vault::compliance.index', $this->data);
    }

    /**
     * Store a new compliance document record.
     */
    public function store(Request $request)
    {
        abort_403(!$this->user->permission('manage_compliance_docs'));

        $request->validate([
            'compliance_type' => 'required|in:insurance,police_check,wwcc,sds,other',
            'document_id'     => 'nullable|exists:vault_documents,id',
            'staff_id'        => 'nullable|exists:users,id',
            'chemical_name'   => 'nullable|string|max:255',
            'expiry_date'     => 'nullable|date',
        ]);

        VaultComplianceDocument::create([
            'company_id'      => $this->user->company_id,
            'compliance_type' => $request->input('compliance_type'),
            'document_id'     => $request->input('document_id'),
            'staff_id'        => $request->input('staff_id'),
            'chemical_name'   => $request->input('chemical_name'),
            'expiry_date'     => $request->input('expiry_date'),
        ]);

        return Reply::success('Compliance document added successfully.');
    }

    /**
     * Expiry dashboard — summary cards of expired, expiring soon, and recent uploads.
     */
    public function dashboard()
    {
        abort_403(!$this->user->permission('manage_compliance_docs'));

        $companyId = $this->user->company_id;

        $this->expiredCount      = VaultComplianceDocument::where('company_id', $companyId)->expired()->count();
        $this->expiringSoonCount = VaultComplianceDocument::where('company_id', $companyId)->expiringSoon(30)->count();
        $this->recentUploads     = VaultComplianceDocument::with('document', 'staff')
            ->where('company_id', $companyId)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $this->byType = VaultComplianceDocument::where('company_id', $companyId)
            ->selectRaw('compliance_type, count(*) as total')
            ->groupBy('compliance_type')
            ->pluck('total', 'compliance_type')
            ->toArray();

        return view('titan_vault::compliance.dashboard', $this->data);
    }
}
