<?php

namespace Modules\ProShots\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPebblely extends Model
{
    protected $table = 'pebblely';

    protected $fillable = [
        'user_id',
        'image',
        'job_ref',
        'room_type',
        'photo_stage',
        'is_published_to_vault',
        'vault_document_id',
    ];

    protected $casts = [
        'is_published_to_vault' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function jobBatch(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(JobBatch::class, 'job_ref', 'job_ref');
    }
}
