<?php

namespace Modules\CleanQuality\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\CleanQuality\Traits\CompanyScoped;

class InspectionTemplateItem extends Model
{
    use CompanyScoped;
    use HasFactory;

    protected $table = 'inspection_template_items';

    protected $fillable = [
        'template_id',
        'item_name',
        'standard',
        'sort_order',
        'is_required',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    public function template()
    {
        return $this->belongsTo(InspectionTemplate::class, 'template_id');
    }
}