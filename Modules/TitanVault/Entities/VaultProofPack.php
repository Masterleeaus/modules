<?php

namespace Modules\TitanVault\Entities;

use App\Models\BaseModel;
use App\Models\User;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class VaultProofPack extends BaseModel
{
    use HasCompany;

    protected $table = 'vault_proof_packs';

    const STATUS_DRAFT    = 'draft';
    const STATUS_SENT     = 'sent';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'company_id',
        'job_ref',
        'job_id',
        'title',
        'status',
        'created_by',
        'sent_at',
        'approved_at',
        'approval_token',
        'client_email',
        'client_name',
    ];

    protected $casts = [
        'sent_at'     => 'datetime',
        'approved_at' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function documents(): BelongsToMany
    {
        return $this->belongsToMany(
            VaultDocument::class,
            'vault_proof_pack_documents',
            'proof_pack_id',
            'document_id'
        )->withPivot('sort_order')->orderByPivot('sort_order');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_DRAFT)
                     ->orWhere('status', self::STATUS_SENT);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    // -------------------------------------------------------------------------
    // Methods
    // -------------------------------------------------------------------------

    /**
     * Generate a unique approval token and persist it.
     */
    public function generateApprovalToken(): string
    {
        $token = Str::random(64);
        $this->update(['approval_token' => $token]);
        return $token;
    }
}
