<?php

namespace Modules\CleanQuality\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\CleanQuality\Traits\CompanyScoped;

class InspectionTemplate extends Model
{
    use CompanyScoped;
    use HasFactory;

    protected $table = 'inspection_templates';

    protected $fillable = [
        'name',
        'trade',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(InspectionTemplateItem::class, 'template_id')->orderBy('sort_order');
    }
}