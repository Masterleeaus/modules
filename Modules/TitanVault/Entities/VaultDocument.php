<?php

namespace Modules\TitanVault\Entities;

use App\Models\BaseModel;
use App\Models\User;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class VaultDocument extends BaseModel
{
    use HasCompany;

    protected $table = 'vault_documents';

    // Status constants
    const DRAFT     = 'draft';
    const IN_REVIEW = 'in_review';
    const APPROVED  = 'approved';
    const REJECTED  = 'rejected';
    const ARCHIVED  = 'archived';

    protected $fillable = [
        'company_id',
        'title',
        'description',
        'file_path',
        'content',
        'mime_type',
        'version',
        'parent_document_id',
        'created_by',
        'project_id',
        'client_id',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function versions(): HasMany
    {
        return $this->hasMany(VaultDocumentVersion::class, 'document_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(VaultDocumentComment::class, 'document_id');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(VaultApproval::class, 'document_id');
    }

    public function accessLinks(): HasMany
    {
        return $this->hasMany(VaultAccessLink::class, 'document_id');
    }

    public function activityLog(): HasMany
    {
        return $this->hasMany(VaultActivityLog::class, 'document_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function project(): BelongsTo
    {
        // Soft FK – no hard dependency on a specific Projects module.
        return $this->belongsTo(\App\Models\Project::class, 'project_id');
    }

    public function client(): BelongsTo
    {
        // In WorkSuite, clients are users. client_id references the users table.
        return $this->belongsTo(User::class, 'client_id');
    }

    public function latestVersion(): HasOne
    {
        return $this->hasOne(VaultDocumentVersion::class, 'document_id')
            ->latestOfMany('version_number');
    }

    public function parentDocument(): BelongsTo
    {
        return $this->belongsTo(VaultDocument::class, 'parent_document_id');
    }
}
