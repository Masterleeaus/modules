<?php

namespace Modules\Biometric\Entities;

use App\Models\BaseModel;
use App\Traits\HasCompany;
use Illuminate\Support\Facades\Log;

class BiometricSetting extends BaseModel
{

    use HasCompany;

    protected $guarded = ['id'];
}
