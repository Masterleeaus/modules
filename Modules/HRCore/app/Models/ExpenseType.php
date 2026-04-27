<?php

namespace Modules\HRCore\app\Models;

use App\Enums\Status;
use App\Traits\UserActionsTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class ExpenseType extends Model implements AuditableContract
{
    use Auditable, HasFactory, SoftDeletes, UserActionsTrait;

    protected $table = 'expense_types';

    protected $fillable = [
        'name',
        'code',
        'description',
        'default_amount',
        'max_amount',
        'requires_receipt',
        'requires_approval',
        'status',
        'gl_account_code',
        'category',
        'tenant_id',
        'created_by_id',
        'updated_by_id',
    ];

    protected $casts = [
        'status' => Status::class,
        'default_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'requires_receipt' => 'boolean',
        'requires_approval' => 'boolean',
    ];

    /**
     * Get the expense requests for this type
     */
    public function expenseRequests()
    {
        return $this->hasMany(ExpenseRequest::class, 'expense_type_id');
    }

    /**
     * Scope for active expense types
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', Status::ACTIVE);
    }

    /**
     * Get the display name with code
     */
    public function getDisplayNameAttribute(): string
    {
        return "{$this->name} ({$this->code})";
    }

    /**
     * Check if amount is within limits
     */
    public function isAmountValid(float $amount): bool
    {
        if ($this->max_amount && $amount > $this->max_amount) {
            return false;
        }

        return true;
    }

    /**
     * Get validation rules for expense request
     */
    public function getValidationRules(): array
    {
        $rules = [];

        if ($this->requires_receipt) {
            $rules['attachments'] = 'required';
        }

        if ($this->max_amount) {
            $rules['amount'] = "required|numeric|min:0.01|max:{$this->max_amount}";
        } else {
            $rules['amount'] = 'required|numeric|min:0.01';
        }

        return $rules;
    }

    /**
     * Get categories
     */
    public static function getCategories(): array
    {
        return [
            'travel' => __('Travel'),
            'meals' => __('Meals & Entertainment'),
            'office' => __('Office Supplies'),
            'utilities' => __('Utilities'),
            'professional' => __('Professional Services'),
            'marketing' => __('Marketing'),
            'other' => __('Other'),
        ];
    }
}
