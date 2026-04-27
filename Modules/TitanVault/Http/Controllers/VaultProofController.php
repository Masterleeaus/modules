<?php

namespace Modules\TitanVault\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Modules\TitanVault\Entities\VaultAccessLink;
use Modules\TitanVault\Entities\VaultActivityLog;
use Modules\TitanVault\Entities\VaultApproval;
use Modules\TitanVault\Entities\VaultDocument;
use Modules\TitanVault\Entities\VaultDocumentComment;

class VaultProofController extends Controller
{
    /**
     * Show the public proof review page.
     */
    public function show(Request $request, string $token)
    {
        $link = VaultAccessLink::where('token', $token)->first();

        if (!$link) {
            abort(404);
        }

        if (!$link->isValid()) {
            abort(403, __('titan_vault::titan_vault.link_expired_or_inactive'));
        }

        // Password protection: redirect to password form if not yet verified.
        if ($link->password_hash && !session("vault_proof_auth_{$token}")) {
            return redirect()->route('titan-vault.proof.password', $token);
        }

        $document = $link->document()->with(['versions', 'comments', 'approvals'])->firstOrFail();

        // Increment view count and log activity.
        $link->increment('view_count');

        VaultActivityLog::create([
            'document_id'  => $document->id,
            'client_token' => $token,
            'action'       => 'viewed',
            'ip_address'   => $request->ip(),
        ]);

        return view('titan_vault::proof.show', compact('document', 'link', 'token'));
    }

    /**
     * Show the password entry form for a protected proof link.
     */
    public function password(Request $request, string $token)
    {
        $link = VaultAccessLink::where('token', $token)->first();

        if (!$link) {
            abort(404);
        }

        if (!$link->is_active || $link->isExpired()) {
            abort(403, __('titan_vault::titan_vault.link_expired_or_inactive'));
        }

        if ($request->isMethod('post')) {
            $request->validate(['password' => 'required|string']);

            if (Hash::check($request->input('password'), $link->password_hash)) {
                session(["vault_proof_auth_{$token}" => true]);
                return redirect()->route('titan-vault.proof.show', $token);
            }

            return back()->withErrors(['password' => __('titan_vault::titan_vault.invalid_password')]);
        }

        return view('titan_vault::proof.password', compact('token'));
    }

    /**
     * Record an approval for the proof.
     */
    public function approve(Request $request, string $token)
    {
        $link = $this->resolveActiveLink($token);

        $request->validate([
            'approver_name'  => 'required|string|max:255',
            'approver_email' => 'required|email|max:255',
            'signature_data' => 'nullable|string',
        ]);

        $document = $link->document;

        VaultApproval::create([
            'document_id'    => $document->id,
            'approver_name'  => $request->input('approver_name'),
            'approver_email' => $request->input('approver_email'),
            'ip_address'     => $request->ip(),
            'approved_at'    => now(),
            'signature_data' => $request->input('signature_data'),
            'action'         => VaultApproval::ACTION_APPROVED,
        ]);

        $document->update(['status' => VaultDocument::APPROVED]);

        VaultActivityLog::create([
            'document_id'  => $document->id,
            'client_token' => $token,
            'action'       => 'approved',
            'ip_address'   => $request->ip(),
            'metadata'     => ['approver_email' => $request->input('approver_email')],
        ]);

        return back()->with('success', __('titan_vault::titan_vault.proof_approved'));
    }

    /**
     * Request a revision on the proof.
     */
    public function requestRevision(Request $request, string $token)
    {
        $link = $this->resolveActiveLink($token);

        $request->validate([
            'approver_name'  => 'required|string|max:255',
            'approver_email' => 'required|email|max:255',
            'revision_notes' => 'required|string',
        ]);

        $document = $link->document;

        VaultApproval::create([
            'document_id'    => $document->id,
            'approver_name'  => $request->input('approver_name'),
            'approver_email' => $request->input('approver_email'),
            'ip_address'     => $request->ip(),
            'action'         => VaultApproval::ACTION_REVISION_REQUESTED,
            'revision_notes' => $request->input('revision_notes'),
        ]);

        VaultDocumentComment::create([
            'document_id'  => $document->id,
            'client_token' => $token,
            'content'      => $request->input('revision_notes'),
        ]);

        $document->update(['status' => VaultDocument::REJECTED]);

        VaultActivityLog::create([
            'document_id'  => $document->id,
            'client_token' => $token,
            'action'       => 'revision_requested',
            'ip_address'   => $request->ip(),
            'metadata'     => ['approver_email' => $request->input('approver_email')],
        ]);

        return back()->with('success', __('titan_vault::titan_vault.revision_requested'));
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    protected function resolveActiveLink(string $token): VaultAccessLink
    {
        $link = VaultAccessLink::with('document')->where('token', $token)->first();

        if (!$link) {
            abort(404);
        }

        if (!$link->isValid()) {
            abort(403, __('titan_vault::titan_vault.link_expired_or_inactive'));
        }

        return $link;
    }
}
