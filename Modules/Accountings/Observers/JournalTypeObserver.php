<?php

namespace Modules\Accountings\Observers;

use Modules\Accountings\Entities\JournalType;

class JournalTypeObserver
{

    public function saving(JournalType $unit)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $unit->company_id = company()->id;
        }
    }

}

