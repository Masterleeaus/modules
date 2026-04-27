<?php

namespace Modules\Accountings\Observers;

use Modules\Accountings\Entities\Pnl;

class PnlObserver
{

    public function saving(Pnl $unit)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $unit->company_id = company()->id;
        }
    }

}

