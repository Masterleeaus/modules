<?php

namespace Modules\HRCore\app\Models;

use App\Enums\Status;
use App\Models\User;
use App\Traits\UserActionsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Team extends Model implements AuditableContract
{
    use Auditable, HasFactory, SoftDeletes, UserActionsTrait;

    protected $table = 'teams';

    protected $fillable = [
        'team_head_id',
        'name',
        'code',
        'notes',
        'status',
        'created_by_id',
        'updated_by_id',
        'tenant_id',
    ];

    public $casts = [
        'status' => Status::class,
    ];

    public function teamHead()
    {
        return $this->belongsTo(User::class, 'team_head_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'team_id');
    }
}
