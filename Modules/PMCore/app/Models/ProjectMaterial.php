<?php

namespace Modules\PMCore\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectMaterial extends Model
{
    protected $table = 'project_materials';

    protected $fillable = [
        'project_id',
        'material_name',
        'quantity',
        'unit',
        'unit_cost',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_cost' => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the total cost for this material line.
     */
    public function getTotalCostAttribute(): float
    {
        return (float) $this->quantity * (float) ($this->unit_cost ?? 0);
    }
}
