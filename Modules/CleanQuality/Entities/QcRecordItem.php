<?php

namespace Modules\CleanQuality\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\CleanQuality\Traits\CompanyScoped;

class QcRecordItem extends Model
{
    use CompanyScoped;

    protected $table = 'qc_record_items';

    protected $fillable = [
        'company_id',
        'record_id',
        'item_label',
        'score',
        'weight',
        'notes',
        'photo',
    ];

    protected $casts = [
        'score'  => 'integer',
        'weight' => 'integer',
    ];

    public function record(): BelongsTo
    {
        return $this->belongsTo(QcRecord::class, 'record_id');
    }
}
