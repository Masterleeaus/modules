<?php

namespace Modules\TitanVault\Entities;

use App\Models\BaseModel;
use App\Models\User;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VaultComplianceDocument extends BaseModel
{
    use HasCompany;

    protected $table = 'vault_compliance_documents';

    const TYPE_INSURANCE    = 'insurance';
    const TYPE_POLICE_CHECK = 'police_check';
    const TYPE_WWCC         = 'wwcc';
    const TYPE_SDS          = 'sds';
    const TYPE_OTHER        = 'other';

    protected $fillable = [
        'company_id',
        'document_id',
        'compliance_type',
        'staff_id',
        'chemical_name',
        'expiry_date',
        'alert_sent_at',
    ];

    protected $casts = [
        'expiry_date'   => 'date',
        'alert_sent_at' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function document(): BelongsTo
    {
        return $this->belongsTo(VaultDocument::class, 'document_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Records expiring within the next $days days (inclusive of today).
     */
    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->whereNotNull('expiry_date')
                     ->whereDate('expiry_date', '>=', now()->toDateString())
                     ->whereDate('expiry_date', '<=', now()->addDays($days)->toDateString());
    }

    /**
     * Records where expiry_date is in the past.
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiry_date')
                     ->whereDate('expiry_date', '<', now()->toDateString());
    }
}
