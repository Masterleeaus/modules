<?php

namespace Modules\Recruit\Entities;

use App\Models\BaseModel;
use App\Traits\IconTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecruitJobCustomAnswer extends BaseModel
{
    use IconTrait;

    const FILE_PATH = 'custom-question-files';

    protected $appends = [
        'file_url',
    ];

    protected $with = ['question'];

    public function getTitleAttribute($value)
    {
        return ucwords($value);
    }

    public function getFileUrlAttribute()
    {
        return (! is_null($this->external_link)) ? $this->external_link : asset_url_local_s3('custom-question-files/'.$this->recruit_job_question_id.'/'.$this->hashname);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(RecruitCustomQuestion::class, 'recruit_job_question_id');
    }
}
