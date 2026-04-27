<?php

namespace Modules\Recruit\Entities;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Recruit\Observers\OfferLetterFileObserver;

class RecruitJobOfferLetterFiles extends BaseModel
{
    use HasFactory;

    protected $table = 'recruit_job_offer_files';

    protected $fillable = [];

    public static function boot()
    {
        parent::boot();
        static::observe(OfferLetterFileObserver::class);
    }

    public function getFileUrlAttribute()
    {
        return (! is_null($this->external_link)) ? $this->external_link : asset_url_local_s3('application-files/'.$this->recruit_job_offer_letter_id.'/'.$this->hashname);
    }
}
