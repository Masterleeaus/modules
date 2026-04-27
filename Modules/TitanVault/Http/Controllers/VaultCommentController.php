<?php

namespace Modules\TitanVault\Http\Controllers;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use Illuminate\Http\Request;
use Modules\TitanVault\Entities\VaultDocument;
use Modules\TitanVault\Entities\VaultDocumentComment;

class VaultCommentController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('titan_vault::titan_vault.module_name');
    }

    /**
     * Add a comment to a document.
     */
    public function store(Request $request, int $id)
    {
        abort_403(!$this->user->permission('view_vault_documents'));

        $request->validate([
            'content'          => 'required|string',
            'position'         => 'nullable|string|max:255',
            'parent_comment_id' => 'nullable|integer|exists:vault_document_comments,id',
        ]);

        VaultDocument::findOrFail($id);

        $comment = VaultDocumentComment::create([
            'document_id'       => $id,
            'user_id'           => $this->user->id,
            'content'           => $request->input('content'),
            'position'          => $request->input('position'),
            'parent_comment_id' => $request->input('parent_comment_id'),
        ]);

        return Reply::successWithData(
            __('titan_vault::titan_vault.comment_added'),
            ['comment' => $comment]
        );
    }

    /**
     * Mark a comment as resolved.
     */
    public function resolve(int $id, int $commentId)
    {
        abort_403(!$this->user->permission('edit_vault_documents'));

        $comment = VaultDocumentComment::where('document_id', $id)->findOrFail($commentId);
        $comment->update(['resolved_at' => now()]);

        return Reply::success(__('titan_vault::titan_vault.comment_resolved'));
    }

    /**
     * Delete a comment.
     */
    public function destroy(int $id, int $commentId)
    {
        abort_403(!$this->user->permission('delete_vault_documents'));

        $comment = VaultDocumentComment::where('document_id', $id)->findOrFail($commentId);
        $comment->delete();

        return Reply::success(__('titan_vault::titan_vault.comment_deleted'));
    }
}
