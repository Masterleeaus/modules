<?php

namespace Modules\CleanQuality\Entities;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InspectionItem extends BaseModel
{
    protected $table = 'inspection_items';

    protected $fillable = [
        'inspection_id',
        'area',
        'passed',
        'notes',
        'photo_path',
    ];

    protected $casts = [
        'passed' => 'boolean',
    ];

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class, 'inspection_id');
    }
}
