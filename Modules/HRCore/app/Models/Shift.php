<?php

namespace Modules\HRCore\app\Models;

use App\Enums\Status;
use App\Traits\UserActionsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Shift extends Model implements AuditableContract
{
    use Auditable, HasFactory, SoftDeletes, UserActionsTrait;

    protected $table = 'shifts';

    protected $fillable = [
        'name',
        'code',
        'notes',
        'start_time',
        'end_time',
        'sunday',
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday',
        'status',
        'created_by_id',
        'updated_by_id',
        'tenant_id',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s',
        'sunday' => 'boolean',
        'monday' => 'boolean',
        'tuesday' => 'boolean',
        'wednesday' => 'boolean',
        'thursday' => 'boolean',
        'friday' => 'boolean',
        'saturday' => 'boolean',
        'status' => Status::class,
    ];

    public function users()
    {
        return $this->hasMany(\App\Models\User::class, 'shift_id');
    }
}
