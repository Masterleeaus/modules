<?php

namespace Modules\TitanVault\Http\Controllers;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\TitanVault\Entities\VaultAccessLink;
use Modules\TitanVault\Entities\VaultDocument;

class VaultShareController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('titan_vault::titan_vault.module_name');
    }

    /**
     * List all access links for a document.
     */
    public function index(int $id)
    {
        abort_403(!$this->user->permission('view_vault_documents'));

        $document          = VaultDocument::findOrFail($id);
        $this->document    = $document;
        $this->accessLinks = $document->accessLinks()->with('creator')->orderByDesc('created_at')->get();

        return Reply::dataOnly(['links' => $this->accessLinks]);
    }

    /**
     * Generate a new access/share link for a document.
     */
    public function generate(Request $request, int $id)
    {
        abort_403(!$this->user->permission('edit_vault_documents'));

        $request->validate([
            'expires_at' => 'nullable|date|after_or_equal:today',
            'password'   => 'nullable|string|min:4',
            'max_views'  => 'nullable|integer|min:1',
        ]);

        VaultDocument::findOrFail($id);

        $link = VaultAccessLink::create([
            'document_id'   => $id,
            'token'         => Str::random(64),
            'password_hash' => $request->filled('password')
                ? Hash::make($request->input('password'))
                : null,
            'expires_at'    => $request->input('expires_at'),
            'max_views'     => $request->input('max_views'),
            'created_by'    => $this->user->id,
            'is_active'     => true,
        ]);

        return Reply::successWithData(
            __('titan_vault::titan_vault.link_generated'),
            [
                'link'      => $link,
                'proof_url' => route('titan-vault.proof.show', $link->token),
            ]
        );
    }

    /**
     * Revoke (deactivate) an access link.
     */
    public function revoke(int $id, int $linkId)
    {
        abort_403(!$this->user->permission('edit_vault_documents'));

        $link = VaultAccessLink::where('document_id', $id)->findOrFail($linkId);
        $link->update(['is_active' => false]);

        return Reply::success(__('titan_vault::titan_vault.link_revoked'));
    }
}
