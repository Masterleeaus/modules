<?php

namespace Modules\TitanVault\Http\Controllers;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use Modules\TitanVault\Entities\VaultApproval;
use Modules\TitanVault\Entities\VaultDocument;

class VaultApprovalController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('titan_vault::titan_vault.module_name');
    }

    /**
     * List all approvals for a document.
     */
    public function index(int $id)
    {
        abort_403(!$this->user->permission('view_vault_documents'));

        $document         = VaultDocument::findOrFail($id);
        $this->document   = $document;
        $this->approvals  = $document->approvals()->orderByDesc('created_at')->get();

        return Reply::dataOnly(['approvals' => $this->approvals]);
    }

    /**
     * Delete an approval record.
     */
    public function destroy(int $id, int $approvalId)
    {
        abort_403(!$this->user->permission('delete_vault_documents'));

        $approval = VaultApproval::where('document_id', $id)->findOrFail($approvalId);
        $approval->delete();

        return Reply::success(__('titan_vault::titan_vault.approval_deleted'));
    }
}
