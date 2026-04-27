<?php

namespace Modules\Accountings\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompany;
use Modules\Accountings\Traits\HasUserScope;

class ServiceLine extends Model
{
    use HasCompany, HasUserScope;

    protected bool $includeNullUserScope = true;

    protected $table = 'acc_service_lines';

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
