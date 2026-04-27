<?php

namespace Modules\Recruit\Entities;

use App\Models\BaseModel;
use App\Models\User;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Represents an applicant who has applied for a job (pre-employee state).
 *
 * Applicants in this table are NOT yet employees. When `status` transitions
 * to 'hired', `JobApplicantController@hire` calls `EmployeeController@store`
 * to create the user/employee record and sets `converted_employee_id`.
 *
 * Resumes are stored on the private disk to prevent public exposure.
 * The `converted_employee_id` FK is set on hire to prevent re-hiring the
 * same person as a duplicate employee.
 */
class JobApplicant extends BaseModel
{
    use HasCompany, SoftDeletes;

    protected $table = 'job_applicants';

    protected $fillable = [
        'company_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'position_applied',
        'status',
        'job_posting_id',
        'cover_letter',
        'resume_path',
        'source',
        'availability_date',
        'interview_notes',
        'interviewer_id',
        'offer_sent_at',
        'offer_accepted_at',
        'converted_employee_id',
    ];

    protected $casts = [
        'availability_date'  => 'date',
        'interview_notes'    => 'array',
        'offer_sent_at'      => 'datetime',
        'offer_accepted_at'  => 'datetime',
    ];

    /** Stages of the FSM recruitment pipeline. */
    public const STATUSES = [
        'applied'   => 'Applied',
        'screening' => 'Screening',
        'interview' => 'Interview',
        'offer'     => 'Offer',
        'hired'     => 'Hired',
        'rejected'  => 'Rejected',
    ];

    /** Cleaning-business position types. */
    public const POSITIONS = [
        'cleaner'      => 'Cleaner',
        'supervisor'   => 'Supervisor',
        'area_manager' => 'Area Manager',
        'office'       => 'Office / Admin',
    ];

    /** Application sources. */
    public const SOURCES = [
        'website'  => 'Website',
        'seek'     => 'Seek',
        'indeed'   => 'Indeed',
        'referral' => 'Referral',
    ];

    /** File path on the private disk for resumes. */
    public const RESUME_DISK = 'local';
    public const RESUME_PATH = 'recruit/resumes';

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class, 'job_posting_id');
    }

    public function interviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }

    public function convertedEmployee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'converted_employee_id');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /** Full name derived from first + last. */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /** Whether this applicant has already been converted to an employee. */
    public function isHired(): bool
    {
        return $this->status === 'hired' && !is_null($this->converted_employee_id);
    }
}
