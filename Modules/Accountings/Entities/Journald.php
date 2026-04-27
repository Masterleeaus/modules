<?php
namespace Modules\Accountings\Entities;

use App\Models\BaseModel;
use App\Traits\HasCompany;
use Modules\Accountings\Entities\Accounting;
use Modules\Accountings\Entities\Journal;

class Journald extends BaseModel
{
    use HasCompany;

    protected $table = 'acc_journald';
    protected $guarded = ['id'];

    public function journal()
    {
        return $this->belongsTo(Journal::class, 'journal_id');
    }

    public function coa()
    {
        return $this->belongsTo(Accounting::class, 'coa_id');
    }
}

