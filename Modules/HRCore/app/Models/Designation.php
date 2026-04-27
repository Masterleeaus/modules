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

class Designation extends Model implements AuditableContract
{
    use Auditable, HasFactory, SoftDeletes, UserActionsTrait;

    protected $table = 'designations';

    protected $fillable = [
        'name',
        'code',
        'notes',
        'status',
        'department_id',
        'parent_id',
        'tenant_id',
        'level',
        'created_by_id',
        'updated_by_id',
    ];

    protected $casts = [
        'status' => Status::class,
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function parent()
    {
        return $this->belongsTo(Designation::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Designation::class, 'parent_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'designation_id');
    }
}
