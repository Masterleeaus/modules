<?php

namespace Modules\HRCore\app\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeLifecycleState extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'state',
        'previous_state',
        'effective_date',
        'reason',
        'remarks',
        'approved_by',
        'approved_at',
        'created_by',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the user that this lifecycle state belongs to
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who created this state
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved this state
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
