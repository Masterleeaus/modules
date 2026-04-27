<?php

namespace Modules\Accountings\Entities;

use App\Models\BaseModel;
use Modules\Accountings\Entities\Journal;
use App\Traits\HasCompany;

class JournalType extends BaseModel
{
    use HasCompany;

    protected $table = 'acc_type_journal';
    protected $guarded = ['id'];


    public function acc()
    {
        return $this->hasMany(Journal::class);
    }
}

