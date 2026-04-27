<?php

namespace App\Models;

use App\Models\Concerns\TenantScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecurringJobTemplate extends Model
{
    use HasFactory, SoftDeletes, TenantScoped;

    const FREQUENCY_WEEKLY    = 'weekly';
    const FREQUENCY_BIWEEKLY  = 'biweekly';
    const FREQUENCY_MONTHLY   = 'monthly';
    const FREQUENCY_CUSTOM    = 'custom';

    protected $fillable = [
        'organization_id',
        'customer_id',
        'property_id',
        'job_type_id',
        'assigned_to',
        'title',
        'description',
        'frequency',
        'recurrence_rule',
        'start_date',
        'end_date',
        'price',
        'is_active',
        'last_generated_on',
    ];

    protected function casts(): array
    {
        return [
            'start_date'        => 'date',
            'end_date'          => 'date',
            'last_generated_on' => 'date',
            'price'             => 'decimal:2',
            'is_active'         => 'boolean',
        ];
    }

    public static function frequencies(): array
    {
        return [
            self::FREQUENCY_WEEKLY   => 'Weekly',
            self::FREQUENCY_BIWEEKLY => 'Bi-Weekly',
            self::FREQUENCY_MONTHLY  => 'Monthly',
            self::FREQUENCY_CUSTOM   => 'Custom',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function jobType(): BelongsTo
    {
        return $this->belongsTo(JobType::class);
    }

    public function assignedTechnician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'recurring_template_id');
    }

    /**
     * Get the next scheduled date based on frequency and last generated date.
     */
    public function nextRunDate(): ?\Illuminate\Support\Carbon
    {
        $base = $this->last_generated_on ?? $this->start_date->subDay();

        return match ($this->frequency) {
            self::FREQUENCY_WEEKLY   => $base->copy()->addWeek(),
            self::FREQUENCY_BIWEEKLY => $base->copy()->addWeeks(2),
            self::FREQUENCY_MONTHLY  => $base->copy()->addMonth(),
            default                  => null,
        };
    }
}
