<?php

namespace Modules\Recruit\Entities;

use App\Models\BaseModel;
use App\Traits\IconTrait;
use Modules\Recruit\Observers\InterviewFileObserver;

class RecruitInterviewFile extends BaseModel
{
    use IconTrait;

    protected $appends = ['file_url', 'icon'];

    public static function boot()
    {
        parent::boot();
        static::observe(InterviewFileObserver::class);
    }

    public function getFileUrlAttribute()
    {
        return (! is_null($this->external_link)) ? $this->external_link : asset_url_local_s3('interview-files/'.$this->recruit_interview_schedule_id.'/'.$this->hashname);
    }
}
