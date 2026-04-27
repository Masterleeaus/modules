<?php

namespace Modules\Accountings\Entities;

use App\Models\BaseModel;
use App\Traits\HasCompany;
use Modules\Accountings\Traits\HasUserScope;

class AuditLog extends BaseModel
{
    use HasCompany, HasUserScope;

    protected $table = 'acc_audit_logs';
    public $timestamps = false;

    protected $guarded = ['id'];

    protected $casts = [
        'meta' => 'array',
    ];
}
