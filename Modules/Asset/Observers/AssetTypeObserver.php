<?php

namespace Modules\Asset\Observers;

use Modules\Asset\Entities\AssetType;

class AssetTypeObserver
{
    public function creating(AssetType $model)
    {

        if (company()) {
            $model->company_id = company()->id;
        }
    }
}
