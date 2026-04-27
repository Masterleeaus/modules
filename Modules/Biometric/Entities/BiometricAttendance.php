<?php

namespace Modules\Biometric\Entities;

use App\Models\BaseModel;
use App\Models\User;
use App\Traits\HasCompany;

class BiometricAttendance extends BaseModel
{
    use HasCompany;

    protected $guarded = ['id'];

    protected $table = 'biometric_device_attendances';

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
