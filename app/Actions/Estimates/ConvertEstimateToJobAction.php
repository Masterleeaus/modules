<?php

namespace App\Actions\Estimates;

use App\Models\Estimate;
use App\Models\Job;

class ConvertEstimateToJobAction
{
    /**
     * Convert an accepted estimate into a scheduled job.
     *
     * Preconditions (caller is responsible for enforcing):
     *  - $estimate->status === Estimate::STATUS_ACCEPTED
     *  - $estimate->convertedJob === null (not already converted)
     */
    public function execute(Estimate $estimate): Job
    {
        $estimate->load('packages.lineItems');

        $package = $estimate->packages
            ->firstWhere('tier', $estimate->accepted_package)
            ?? $estimate->packages->first();

        if ($package === null) {
            throw new \RuntimeException('Estimate has no packages to convert.');
        }

        $job = Job::create([
            'organization_id' => $estimate->organization_id,
            'customer_id'     => $estimate->customer_id,
            'estimate_id'     => $estimate->id,
            'title'           => $estimate->title,
            'description'     => $package->description,
            'status'          => Job::STATUS_SCHEDULED,
            'office_notes'    => $estimate->footer,
        ]);

        foreach ($package->lineItems as $idx => $li) {
            $job->lineItems()->create([
                'item_id'     => $li->item_id,
                'name'        => $li->name,
                'description' => $li->description,
                'unit_price'  => $li->unit_price,
                'quantity'    => $li->quantity,
                'sort_order'  => $idx,
            ]);
        }

        return $job;
    }
}
