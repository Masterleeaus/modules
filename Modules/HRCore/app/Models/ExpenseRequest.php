<?php

namespace Modules\HRCore\app\Models;

use App\Enums\ExpenseRequestStatus;
use App\Models\User;
use App\Traits\UserActionsTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\FileManagerCore\Traits\HasFiles;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class ExpenseRequest extends Model implements AuditableContract
{
    use Auditable, HasFactory, HasFiles, SoftDeletes, UserActionsTrait;

    protected $table = 'expense_requests';

    protected $fillable = [
        'expense_number',
        'expense_date',
        'user_id',
        'expense_type_id',
        'amount',
        'approved_amount',
        'currency',
        'title',
        'description',
        'attachments',
        'status',
        'approved_by_id',
        'approved_at',
        'approval_remarks',
        'rejected_by_id',
        'rejected_at',
        'rejection_reason',
        'processed_by_id',
        'processed_at',
        'payment_reference',
        'processing_notes',
        'department_id',
        'project_code',
        'cost_center',
        'tenant_id',
        'created_by_id',
        'updated_by_id',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
        'approved_amount' => 'decimal:2',
        'attachments' => 'array',
        'status' => ExpenseRequestStatus::class,
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->expense_number)) {
                $model->expense_number = self::generateExpenseNumber();
            }
            if (empty($model->currency)) {
                $model->currency = 'USD';
            }
        });
    }

    /**
     * Generate unique expense number
     */
    public static function generateExpenseNumber(): string
    {
        $prefix = 'EXP';
        $year = date('Y');
        $month = date('m');

        // Get the last expense number for this month
        $lastExpense = self::where('expense_number', 'like', "{$prefix}-{$year}{$month}-%")
            ->orderBy('expense_number', 'desc')
            ->first();

        if ($lastExpense) {
            $lastNumber = (int) substr($lastExpense->expense_number, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return "{$prefix}-{$year}{$month}-".str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function expenseType()
    {
        return $this->belongsTo(ExpenseType::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by_id');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by_id');
    }

    /**
     * Scopes
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', ExpenseRequestStatus::PENDING);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', ExpenseRequestStatus::APPROVED);
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', ExpenseRequestStatus::REJECTED);
    }

    public function scopeProcessed(Builder $query): Builder
    {
        return $query->where('status', ExpenseRequestStatus::PROCESSED);
    }

    public function scopeMyExpenses(Builder $query): Builder
    {
        return $query->where('user_id', auth()->id());
    }

    public function scopeRequiringApproval(Builder $query): Builder
    {
        return $query->where('status', ExpenseRequestStatus::PENDING)
            ->whereHas('expenseType', function ($q) {
                $q->where('requires_approval', true);
            });
    }

    /**
     * Accessors
     */
    public function getFormattedAmountAttribute(): string
    {
        return $this->currency.' '.number_format($this->amount, 2);
    }

    public function getFormattedApprovedAmountAttribute(): string
    {
        if (! $this->approved_amount) {
            return '-';
        }

        return $this->currency.' '.number_format($this->approved_amount, 2);
    }

    public function getCanApproveAttribute(): bool
    {
        return $this->status === ExpenseRequestStatus::PENDING &&
               $this->user_id !== auth()->id() &&
               auth()->user() &&
               (auth()->user()->can('hrcore.approve-expense') || auth()->user()->can('hrcore.reject-expense'));
    }

    public function getCanProcessAttribute(): bool
    {
        return $this->status === ExpenseRequestStatus::APPROVED &&
               auth()->user() &&
               auth()->user()->can('hrcore.process-expense');
    }

    public function getCanEditAttribute(): bool
    {
        if (! auth()->user()) {
            return false;
        }

        // User can edit their own pending expense
        if ($this->status === ExpenseRequestStatus::PENDING && $this->user_id === auth()->id()) {
            return true;
        }

        // Admin/HR can edit any pending expense
        return $this->status === ExpenseRequestStatus::PENDING &&
               auth()->user()->can('hrcore.edit-expense');
    }

    public function getCanDeleteAttribute(): bool
    {
        if (! auth()->user()) {
            return false;
        }

        // User can delete their own pending expense
        if ($this->status === ExpenseRequestStatus::PENDING && $this->user_id === auth()->id()) {
            return true;
        }

        // Admin/HR can delete any pending expense
        return $this->status === ExpenseRequestStatus::PENDING &&
               auth()->user()->can('hrcore.delete-expense');
    }

    /**
     * Methods
     */
    public function approve(User $approver, ?float $approvedAmount = null, ?string $remarks = null): bool
    {
        $this->status = ExpenseRequestStatus::APPROVED;
        $this->approved_by_id = $approver->id;
        $this->approved_at = now();
        $this->approved_amount = $approvedAmount ?? $this->amount;
        $this->approval_remarks = $remarks;

        return $this->save();
    }

    /**
     * Get expense receipt documents using FileManagerCore
     */
    public function getExpenseDocuments()
    {
        return $this->filesByType(\Modules\FileManagerCore\Enums\FileType::EXPENSE_RECEIPT);
    }

    /**
     * Check if expense has attachments using FileManagerCore
     */
    public function hasAttachments(): bool
    {
        return $this->hasFiles();
    }

    public function reject(User $rejector, string $reason): bool
    {
        $this->status = ExpenseRequestStatus::REJECTED;
        $this->rejected_by_id = $rejector->id;
        $this->rejected_at = now();
        $this->rejection_reason = $reason;

        return $this->save();
    }

    public function markAsProcessed(User $processor, ?string $paymentReference = null, ?string $notes = null): bool
    {
        if ($this->status !== ExpenseRequestStatus::APPROVED) {
            return false;
        }

        $this->status = ExpenseRequestStatus::PROCESSED;
        $this->processed_by_id = $processor->id;
        $this->processed_at = now();
        $this->payment_reference = $paymentReference;
        $this->processing_notes = $notes;

        return $this->save();
    }
}
