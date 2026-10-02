<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowTransition extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'organization_id',
        'entity_type',
        'entity_id',
        'from_state',
        'to_state',
        'transition',
        'triggered_by',
        'context',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'context'    => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function triggeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by');
    }
}
