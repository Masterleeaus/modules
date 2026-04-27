<?php

namespace Modules\TitanVault\Http\Controllers;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\TitanVault\Entities\VaultAccessLink;
use Modules\TitanVault\Entities\VaultDocument;
use Modules\TitanVault\Entities\VaultDocumentVersion;

class VaultDocumentController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('titan_vault::titan_vault.module_name');
    }

    public function index(Request $request)
    {
        abort_403(!$this->user->permission('view_vault_documents'));

        $query = VaultDocument::with(['creator', 'latestVersion'])
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->input('project_id'));
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->input('client_id'));
        }

        $this->documents = $query->paginate(20)->withQueryString();
        $this->statuses  = [
            VaultDocument::DRAFT,
            VaultDocument::IN_REVIEW,
            VaultDocument::APPROVED,
            VaultDocument::REJECTED,
            VaultDocument::ARCHIVED,
        ];

        return view('titan_vault::documents.index', $this->data);
    }

    public function create()
    {
        abort_403(!$this->user->permission('add_vault_documents'));

        return view('titan_vault::documents.create', $this->data);
    }

    public function store(Request $request)
    {
        abort_403(!$this->user->permission('add_vault_documents'));

        $request->validate([
            'title'  => 'required|string|max:255',
            'status' => 'nullable|in:draft,in_review,approved,rejected,archived',
        ]);

        $document = new VaultDocument();
        $document->fill($request->only([
            'title', 'description', 'content', 'mime_type',
            'project_id', 'client_id', 'status', 'expires_at',
        ]));
        $document->created_by = $this->user->id;
        $document->status     = $request->input('status', VaultDocument::DRAFT);
        $document->version    = 1;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store(config('titan_vault.storage_path'), config('titan_vault.storage_disk'));
            $document->file_path  = $path;
            $document->mime_type  = $file->getMimeType();
        }

        $document->save();

        return Reply::successWithData(
            __('titan_vault::titan_vault.document_created'),
            ['redirectUrl' => route('titan-vault.documents.show', $document->id)]
        );
    }

    public function show(int $id)
    {
        abort_403(!$this->user->permission('view_vault_documents'));

        $this->document = VaultDocument::with([
            'creator', 'versions.creator', 'comments.user',
            'approvals', 'accessLinks.creator',
        ])->findOrFail($id);

        return view('titan_vault::documents.show', $this->data);
    }

    public function edit(int $id)
    {
        abort_403(!$this->user->permission('edit_vault_documents'));

        $this->document = VaultDocument::findOrFail($id);

        return view('titan_vault::documents.edit', $this->data);
    }

    public function update(Request $request, int $id)
    {
        abort_403(!$this->user->permission('edit_vault_documents'));

        $request->validate([
            'title'  => 'required|string|max:255',
            'status' => 'nullable|in:draft,in_review,approved,rejected,archived',
        ]);

        $document = VaultDocument::findOrFail($id);

        // Snapshot the current state as a new version before updating.
        VaultDocumentVersion::create([
            'document_id'    => $document->id,
            'version_number' => $document->version,
            'content'        => $document->content,
            'file_path'      => $document->file_path,
            'created_by'     => $this->user->id,
            'notes'          => 'Auto-saved before edit.',
        ]);

        $document->fill($request->only([
            'title', 'description', 'content', 'mime_type',
            'project_id', 'client_id', 'status', 'expires_at',
        ]));

        $document->version = $document->version + 1;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store(config('titan_vault.storage_path'), config('titan_vault.storage_disk'));
            $document->file_path = $path;
            $document->mime_type = $file->getMimeType();
        }

        $document->save();

        return Reply::successWithData(
            __('titan_vault::titan_vault.document_updated'),
            ['redirectUrl' => route('titan-vault.documents.show', $document->id)]
        );
    }

    public function destroy(int $id)
    {
        abort_403(!$this->user->permission('delete_vault_documents'));

        VaultDocument::findOrFail($id)->delete();

        return Reply::success(__('titan_vault::titan_vault.document_deleted'));
    }

    public function archive(int $id)
    {
        abort_403(!$this->user->permission('edit_vault_documents'));

        $document = VaultDocument::findOrFail($id);
        $document->update(['status' => VaultDocument::ARCHIVED]);

        return Reply::success(__('titan_vault::titan_vault.document_archived'));
    }

    public function sendForReview(int $id)
    {
        abort_403(!$this->user->permission('edit_vault_documents'));

        $document = VaultDocument::findOrFail($id);
        $document->update(['status' => VaultDocument::IN_REVIEW]);

        $expiryDays = (int) setting('titan_vault_default_expiry_days', config('titan_vault.default_expiry_days', 30));

        $link = VaultAccessLink::create([
            'document_id' => $document->id,
            'token'       => Str::random(64),
            'created_by'  => $this->user->id,
            'is_active'   => true,
            'expires_at'  => $expiryDays > 0 ? now()->addDays($expiryDays) : null,
        ]);

        $proofUrl = route('titan-vault.proof.show', $link->token);

        return Reply::successWithData(
            __('titan_vault::titan_vault.sent_for_review'),
            ['proof_url' => $proofUrl]
        );
    }

    public function versions(int $id)
    {
        abort_403(!$this->user->permission('view_vault_documents'));

        $document        = VaultDocument::findOrFail($id);
        $this->document  = $document;
        $this->versions  = $document->versions()->with('creator')->orderByDesc('version_number')->get();

        return view('titan_vault::documents.show', $this->data);
    }

    public function restoreVersion(int $id, int $versionId)
    {
        abort_403(!$this->user->permission('edit_vault_documents'));

        $document = VaultDocument::findOrFail($id);
        $version  = VaultDocumentVersion::where('document_id', $id)->findOrFail($versionId);

        // Snapshot the current state first.
        VaultDocumentVersion::create([
            'document_id'    => $document->id,
            'version_number' => $document->version,
            'content'        => $document->content,
            'file_path'      => $document->file_path,
            'created_by'     => $this->user->id,
            'notes'          => 'Auto-saved before version restore.',
        ]);

        $document->update([
            'content'   => $version->content,
            'file_path' => $version->file_path,
            'version'   => $document->version + 1,
        ]);

        return Reply::success(__('titan_vault::titan_vault.version_restored'));
    }
}
