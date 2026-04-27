<?php

namespace Modules\Recruit\Entities;

use App\Models\BaseModel;
use App\Models\CompanyAddress;
use App\Models\User;
use App\Traits\CustomFieldsTrait;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class RecruitJobApplication extends BaseModel
{
    use Notifiable, HasCompany, SoftDeletes, CustomFieldsTrait;

    protected $dates = ['end_date', 'start_date', 'date_of_birth', 'deleted_at'];

    protected $fillable = ['name', 'email', 'phone', 'gender', 'status_id'];

    const FILE_PATH = 'job_resume';

    public function getImageUrlAttribute()
    {
        $gravatarHash = md5(strtolower(trim($this->email)));

        return ($this->photo) ? asset_url_local_s3('avatar/'.$this->photo) : 'https://www.gravatar.com/avatar/'.$gravatarHash.'.png?s=200&d=mp';
    }

    public function getFileUrlAttribute()
    {
        return asset_url_local_s3(RecruitJobApplication::FILE_PATH . '/' . $this->id . '/' . $this->resume);
    }

    public function hasGravatar($email)
    {
        // Craft a potential url and test its headers
        $hash = md5(strtolower(trim($email)));

        $uri = 'http://www.gravatar.com/avatar/'.$hash.'?d=404';
        $headers = @get_headers($uri);

        $has_valid_avatar = true;

        try {
            if (! preg_match('|200|', $headers[0])) {
                $has_valid_avatar = false;
            }
        } catch (\Exception $e) {
            $has_valid_avatar = true;
        }

        return $has_valid_avatar;
    }

    public function getTitleAttribute($value)
    {
        return ucwords($value);
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(RecruitJob::class, 'recruit_job_id')->withTrashed();
    }

    public function applicationStatus(): BelongsTo
    {
        return $this->belongsTo(RecruitApplicationStatus::class, 'recruit_application_status_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(RecruitApplicantNote::class, 'recruit_job_application_id')->orderByDesc('id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(RecruitApplicationFile::class, 'recruit_job_application_id')->orderByDesc('id');
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(ApplicationSource::class, 'application_source_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(CompanyAddress::class, 'location_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function followup(): HasOne
    {
        return $this->hasOne(RecruitCandidateFollowUp::class, 'recruit_job_application_id')->orderByDesc('created_at');
    }
}
