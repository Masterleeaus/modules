<?php

namespace Modules\Accountings\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JournalEntry extends Model
{
    protected $table = 'journal_entries';

    protected $fillable = [
        'organization_id',
        'reference_type',
        'reference_id',
        'description',
        'entry_date',
    ];

    protected $casts = [
        'entry_date' => 'date',
    ];

    public function lines(): HasMany
    {
        return $this->hasMany(JournalLine::class);
    }

    public function scopeForOrganization($query, int $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    public function isBalanced(): bool
    {
        $totals = $this->lines()
            ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
            ->first();

        return round((float) $totals->total_debit, 2) === round((float) $totals->total_credit, 2);
    }
}
