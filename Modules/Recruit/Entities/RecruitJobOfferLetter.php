<?php

namespace Modules\Recruit\Entities;

use App\Models\BaseModel;
use App\Models\User;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Modules\Recruit\Observers\OfferLetterObserver;

class RecruitJobOfferLetter extends BaseModel
{

    use Notifiable, HasCompany;

    protected $table = 'recruit_job_offer_letter';

    protected $dates = ['jobExpireDate', 'expJoinDate', 'created_at', 'offer_accept_at', 'job_expire', 'expected_joining_date'];

    protected $fillable = [];

    public static function boot()
    {
        parent::boot();
        static::observe(OfferLetterObserver::class);
    }

    public function getFileUrlAttribute()
    {
        return (!is_null($this->external_link)) ? $this->external_link : asset_url_local_s3('offer/accept/' . $this->sign_image);
    }

    public function files(): HasMany
    {
        return $this->hasMany(RecruitJobOfferLetterFiles::class, 'recruit_job_offer_letter_id')->orderByDesc('id');
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(RecruitJob::class, 'recruit_job_id')->withTrashed();
    }

    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(RecruitJobApplication::class, 'recruit_job_application_id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

}
