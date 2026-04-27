<?php

namespace Modules\Accountings\Entities;

use App\Traits\CustomFieldsTrait;
use App\Traits\HasCompany;
use App\Models\BaseModel;
use Modules\Accountings\Entities\Journald;
use Modules\Accountings\Entities\JournalType;

class Journal extends BaseModel
{
    use CustomFieldsTrait;
    use HasCompany;

    protected $table = 'acc_journalh';
    protected $guarded = ['id'];

    const CUSTOM_FIELD_MODEL = 'App\Models\Invoice';

    public function items()
    {
        return $this->hasMany(Journald::class, 'journal_id');
    }

    public function type()
    {
        return $this->belongsTo(JournalType::class, 'typejournal_id');
    }

    public static function lastInvoiceNumber()
    {
        return (int)Journal::max('id');
    }
}
