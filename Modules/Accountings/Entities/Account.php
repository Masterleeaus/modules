<?php

namespace Modules\Accountings\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    protected $table = 'accounts';

    protected $fillable = [
        'organization_id',
        'code',
        'name',
        'type',
        'is_system',
        'xero_account_id',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    public function journalLines(): HasMany
    {
        return $this->hasMany(JournalLine::class);
    }

    public function scopeRevenue($query)
    {
        return $query->where('type', 'revenue');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopeAsset($query)
    {
        return $query->where('type', 'asset');
    }

    public function scopeForOrganization($query, int $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }
}
