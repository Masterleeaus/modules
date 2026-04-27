<?php

namespace Modules\Recruit\Observers;

use Modules\Recruit\Entities\RecruitApplicationFile;

class ApplicationFilesObserver
{
    public function saving(RecruitApplicationFile $model)
    {
        if (! isRunningInConsoleOrSeeding() && user()) {
            $model->last_updated_by = user()->id;
        }
    }

    public function creating(RecruitApplicationFile $model)
    {
        if (! isRunningInConsoleOrSeeding() && user()) {
            $model->added_by = user()->id;
        }
    }
}
