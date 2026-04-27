<?php

namespace Modules\Biometric\Entities;

use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Model;
use Modules\Biometric\Traits\CompanyScoped;

class BiometricDevice extends Model
{
    use CompanyScoped;
    use HasCompany;

    protected $guarded = ['id'];

    protected $casts = [
        'last_online' => 'datetime',
    ];
}