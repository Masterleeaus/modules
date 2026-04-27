<?php

namespace Modules\TitanVault\Entities;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VaultApproval extends BaseModel
{
    protected $table = 'vault_approvals';

    const ACTION_APPROVED           = 'approved';
    const ACTION_REVISION_REQUESTED = 'revision_requested';

    protected $fillable = [
        'document_id',
        'approver_name',
        'approver_email',
        'ip_address',
        'approved_at',
        'signature_data',
        'action',
        'revision_notes',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function document(): BelongsTo
    {
        return $this->belongsTo(VaultDocument::class, 'document_id');
    }
}
