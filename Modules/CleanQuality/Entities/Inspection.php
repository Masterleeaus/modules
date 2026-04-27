<?php

namespace Modules\CleanQuality\Entities;

use App\Models\User;
use App\Models\BaseModel;
use App\Traits\HasCompany;
use App\Scopes\ActiveScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\CleanQuality\Support\Enums\InspectionStatus;

class Inspection extends BaseModel
{
    use HasCompany, SoftDeletes;

    protected $table = 'inspections';

    protected $fillable = [
        'company_id',
        'booking_id',
        'inspector_id',
        'template_id',
        'score',
        'status',
        'notes',
        'inspected_at',
        'approved_at',
        'approved_by',
        'reclean_booking_id',
    ];

    protected $casts = [
        'score'        => 'decimal:2',
        'inspected_at' => 'datetime',
        'approved_at'  => 'datetime',
    ];

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_id')
            ->withoutGlobalScope(ActiveScope::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by')
            ->withoutGlobalScope(ActiveScope::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(InspectionTemplate::class, 'template_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InspectionItem::class, 'inspection_id')->orderBy('id');
    }

    public function isPassed(): bool
    {
        return $this->status === InspectionStatus::PASSED;
    }

    public function isFailed(): bool
    {
        return $this->status === InspectionStatus::FAILED;
    }

    public function isRecleanBooked(): bool
    {
        return $this->status === InspectionStatus::RECLEAN_BOOKED;
    }

    public function passedItemCount(): int
    {
        return $this->items->where('passed', true)->count();
    }

    public function failedItemCount(): int
    {
        return $this->items->where('passed', false)->count();
    }
}
