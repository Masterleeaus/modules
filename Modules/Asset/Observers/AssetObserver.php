<?php

namespace Modules\Asset\Observers;

use Froiden\RestAPI\Exceptions\ApiException;
use Modules\Asset\Entities\Asset;

class AssetObserver
{
    public function saving(Asset $asset)
    {
        if (! isRunningInConsoleOrSeeding() && user()) {
            $asset->last_updated_by = user()->id;
        }
    }

    public function creating(Asset $asset)
    {
        if (! isRunningInConsoleOrSeeding() && user()) {
            $asset->added_by = user()->id;
        }

        //region Field conditions

        if ($asset->status === 'lent') {
            // New asset cannot have lent status
            $asset->status = 'available';
        }

        if (company()) {
            $asset->company_id = company()->id;
        }

        //endregion
    }

    public function updating(Asset $asset)
    {
        //region Field conditions

        $prevAsset = Asset::findOrFail($asset->id);

        if ($prevAsset->status == 'lent' && $asset->status == 'non_functional') {
            // Cannot set status to non_function from lent. First, asset should be returned
            //phpcs:ignore
            throw new ApiException('Asset should be returned before setting status to non functional', null, 422, 422, 2016);
        }

        //endregion
    }
}
