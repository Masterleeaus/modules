<?php

namespace Modules\Recruit\Observers;

use App\Models\ApplicationSource;
use Modules\Recruit\Entities\ApplicationSource as EntitiesApplicationSource;

class RecruitSourceObserver
{
    /**
     * Handle the ApplicationSource "created" event.
     */
    public function creating(EntitiesApplicationSource $applicationSource): void
    {
        if (company()) {
            $applicationSource->company_id = company()->id;
        }
    }

    /**
     * Handle the ApplicationSource "updated" event.
     */
    // public function updated(ApplicationSource $applicationSource): void
    // {
    //     //
    // }

    /**
     * Handle the ApplicationSource "deleted" event.
     */
    // public function deleted(ApplicationSource $applicationSource): void
    // {
    //     //
    // }

    /**
     * Handle the ApplicationSource "restored" event.
     */
    public function restored(ApplicationSource $applicationSource): void
    {
        //
    }

    /**
     * Handle the ApplicationSource "force deleted" event.
     */
    // public function forceDeleted(ApplicationSource $applicationSource): void
    // {
    //     //
    // }
}
