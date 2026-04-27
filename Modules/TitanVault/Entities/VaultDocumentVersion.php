<?php

namespace Modules\TitanVault\Entities;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VaultDocumentVersion extends BaseModel
{
    protected $table = 'vault_document_versions';

    protected $fillable = [
        'document_id',
        'version_number',
        'content',
        'file_path',
        'created_by',
        'notes',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function document(): BelongsTo
    {
        return $this->belongsTo(VaultDocument::class, 'document_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
