<?php

namespace Modules\TitanGo\Services;

use App\Models\Job;

class TechnicianContextService
{
    /**
     * Assemble a structured context payload for a job that can be consumed by
     * an AI assist layer (e.g. Titan Zero).
     *
     * @return array<string, mixed>
     */
    public function forJob(Job $job): array
    {
        $job->loadMissing(['customer', 'property', 'jobType', 'checklistItems', 'attachments', 'lineItems']);

        $checklistTotal     = $job->checklistItems->count();
        $checklistCompleted = $job->checklistItems->whereNotNull('completed_at')->count();

        $photosBefore = $job->attachments->where('tag', 'before')->count();
        $photosAfter  = $job->attachments->where('tag', 'after')->count();

        return [
            'job' => [
                'id'          => $job->id,
                'title'       => $job->title,
                'status'      => $job->status,
                'type'        => $job->jobType?->name,
                'scheduled_at' => $job->scheduled_at?->toIso8601String(),
            ],
            'customer' => $job->customer ? [
                'name'  => trim($job->customer->first_name . ' ' . $job->customer->last_name),
                'email' => $job->customer->email,
                'phone' => $job->customer->phone ?? null,
            ] : null,
            'property' => $job->property ? [
                'address' => implode(', ', array_filter([
                    $job->property->address_line1,
                    $job->property->city,
                    $job->property->state,
                ])),
                'access_notes' => $job->property->access_notes ?? null,
            ] : null,
            'progress' => [
                'checklist_total'     => $checklistTotal,
                'checklist_completed' => $checklistCompleted,
                'photos_before'       => $photosBefore,
                'photos_after'        => $photosAfter,
                'has_signature'       => $job->attachments->where('tag', 'client_signature')->isNotEmpty(),
            ],
            'notes' => [
                'technician' => $job->technician_notes,
                'customer'   => $job->customer_notes,
            ],
        ];
    }
}
