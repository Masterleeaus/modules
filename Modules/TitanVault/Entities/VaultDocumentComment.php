<?php

namespace Modules\TitanVault\Entities;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VaultDocumentComment extends BaseModel
{
    protected $table = 'vault_document_comments';

    protected $fillable = [
        'document_id',
        'user_id',
        'client_token',
        'position',
        'content',
        'resolved_at',
        'parent_comment_id',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function document(): BelongsTo
    {
        return $this->belongsTo(VaultDocument::class, 'document_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(VaultDocumentComment::class, 'parent_comment_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(VaultDocumentComment::class, 'parent_comment_id');
    }
}
