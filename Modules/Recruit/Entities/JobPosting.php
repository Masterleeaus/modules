<?php

namespace Modules\Recruit\Entities;

use App\Models\BaseModel;
use App\Models\User;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Represents a job vacancy posted by the company.
 *
 * A JobPosting is a public-facing vacancy that can receive JobApplicants.
 */
class JobPosting extends BaseModel
{
    use HasCompany, SoftDeletes;

    protected $table = 'job_postings';

    protected $fillable = [
        'company_id',
        'title',
        'position_type',
        'description',
        'requirements',
        'location',
        'employment_type',
        'pay_rate',
        'pay_unit',
        'status',
        'close_date',
        'slug',
        'created_by',
    ];

    protected $casts = [
        'close_date' => 'date',
    ];

    /** Position types relevant to Australian cleaning/FSM businesses. */
    public const POSITION_TYPES = [
        'cleaner'      => 'Cleaner',
        'supervisor'   => 'Supervisor',
        'area_manager' => 'Area Manager',
        'office'       => 'Office / Admin',
    ];

    public const EMPLOYMENT_TYPES = [
        'casual'         => 'Casual',
        'part_time'      => 'Part Time',
        'full_time'      => 'Full Time',
        'subcontractor'  => 'Subcontractor',
    ];

    public const STATUSES = [
        'draft'     => 'Draft',
        'published' => 'Published',
        'closed'    => 'Closed',
    ];

    public function applicants()
    {
        return $this->hasMany(JobApplicant::class, 'job_posting_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
