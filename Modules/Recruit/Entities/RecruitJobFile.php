<?php

namespace Modules\Recruit\Entities;

use App\Models\BaseModel;
use App\Traits\IconTrait;
use Modules\Recruit\Observers\JobFileObserver;

class RecruitJobFile extends BaseModel
{
    use IconTrait;

    protected $appends = ['file_url', 'icon'];

    public static function boot()
    {
        parent::boot();
        static::observe(JobFileObserver::class);
    }

    public function getFileUrlAttribute()
    {
        return (! is_null($this->external_link)) ? $this->external_link : asset_url_local_s3('job-files/'.$this->recruit_job_id.'/'.$this->hashname);
    }
}
