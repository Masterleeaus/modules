<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobSupplyUsage extends Model
{
    use HasFactory;

    protected $table = 'job_supply_usages';

    protected $fillable = [
        'job_id',
        'item_id',
        'quantity_used',
        'notes',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'quantity_used' => 'decimal:3',
        ];
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
