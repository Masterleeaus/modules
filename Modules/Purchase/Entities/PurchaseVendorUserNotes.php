<?php

namespace Modules\Purchase\Entities;

use App\Models\BaseModel;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseVendorUserNotes extends BaseModel
{

    use HasFactory, HasCompany;

    protected $fillable = [];

    protected static function newFactory()
    {

        return \Modules\Purchase\Database\factories\PurchaseVendorUserNotesFactory::new();

    }

}
