<?php

namespace Modules\Recruit\Entities;

use App\Models\BaseModel;
use App\Models\CompanyAddress;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecruitJobAddress extends BaseModel
{
    use HasFactory;

    protected $fillable = [];

    public function location(): BelongsTo
    {
        return $this->belongsTo(CompanyAddress::class, 'company_address_id');
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(RecruitJob::class, 'recruit_job_id', 'id');
    }

    public function jobs(): BelongsTo
    {
        return $this->belongsTo(RecruitJob::class, 'recruit_job_id', 'id');
    }
}
