<?php

namespace Modules\Purchase\Entities;

use App\Models\BaseModel;
use App\Models\User;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseVendorHistory extends BaseModel
{

    use HasFactory, HasCompany;

    protected $fillable = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function vendors(): BelongsTo
    {
        return $this->belongsTo(PurchaseVendor::class, 'purchase_vendor_id');
    }

    public function vendorNotes(): BelongsTo
    {
        return $this->belongsTo(PurchaseVendorNote::class, 'purchase_vendor_notes_id');
    }

    public function vendorContact(): BelongsTo
    {
        return $this->belongsTo(PurchaseVendorContact::class, 'purchase_vendor_contact_id');
    }

}
