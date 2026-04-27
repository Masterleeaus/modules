<?php

namespace Modules\Accountings\Entities;

use App\Models\BaseModel;
use App\Traits\HasCompany;
use Illuminate\Support\Facades\DB;
use Modules\Accountings\Traits\HasUserScope;

class PeriodLock extends BaseModel
{
    use HasCompany, HasUserScope;

    protected $table = 'acc_period_locks';

    protected $fillable = [
        'company_id',
        'user_id',
        'lock_date',
        'locked_by',
        'reason',
    ];

    protected $casts = [
        'lock_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function lockedByUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'locked_by');
    }

    /**
     * Returns true if the given date falls on or before any lock_date for the company.
     */
    public static function isLocked(int $companyId, string $date): bool
    {
        return DB::table('acc_period_locks')
            ->where('company_id', $companyId)
            ->whereDate('lock_date', '>=', $date)
            ->exists();
    }
}
