<?php

namespace Modules\Accountings\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompany;
use Modules\Accountings\Traits\HasUserScope;

class TaxCode extends Model
{
    use HasCompany, HasUserScope;

    /**
     * Allow global template rows (user_id NULL) to be visible to all users,
     * while still scoping tenant-specific rows to the current user.
     */
    protected bool $includeNullUserScope = true;

    protected $table = 'acc_tax_codes';

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
        'rate' => 'decimal:4',
    ];
}
