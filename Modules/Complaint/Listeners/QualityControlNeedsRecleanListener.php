<?php

namespace Modules\Complaint\Listeners;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Modules\Complaint\Entities\Complaint;

/**
 * Listener for integration event emitted by QualityControl (Quality Inspections) module.
 *
 * Event name: quality_control.needs_reclean
 * Payload (array):
 *  - company_id
 *  - user_id
 *  - job_id (optional)
 *  - quality_control_id (inspection schedule id)
 *  - reason (optional)
 */
class QualityControlNeedsRecleanListener
{
    public function handle($payload): void
    {
        // Event dispatcher may pass payload as array or as first arg.
        $data = is_array($payload) ? $payload : (array) $payload;

        $companyId = Arr::get($data, 'company_id');
        $userId = Arr::get($data, 'user_id');
        $jobId = Arr::get($data, 'job_id');
        $qualityControlId = Arr::get($data, 'quality_control_id');
        $reason = Arr::get($data, 'reason', 'needs_reclean');

        if (empty($companyId) || empty($userId) || empty($qualityControlId)) {
            Log::warning('[QualityIssues] needs_reclean event missing required fields', ['payload' => $data]);
            return;
        }

        // Avoid duplicate issues for the same inspection schedule.
        if (schema_has_column('complaint', 'quality_control_id')) {
            $existing = Complaint::where('company_id', $companyId)
                ->where('quality_control_id', $qualityControlId)
                ->first();

            if ($existing) {
                return;
            }
        }

        $subject = 'Re-clean required';
        if (class_exists('Modules\\CleanQuality\\Entities\\Schedule')) {
            try {
                $schedule = \Modules\CleanQuality\Entities\Schedule::find($qualityControlId);
                if ($schedule && !empty($schedule->subject)) {
                    $subject = 'Re-clean required: ' . $schedule->subject;
                }
            } catch (\Throwable $e) {
                // Keep going with a generic subject.
            }
        }

        $issue = new Complaint();
        $issue->company_id = $companyId;
        $issue->user_id = $userId;
        $issue->subject = $subject;
        $issue->status = 'open';
        $issue->priority = 'high';
        $issue->added_by = $userId;
        $issue->last_update_by = $userId;
        // complaint.no_hp is required in this module schema.
        $issue->no_hp = 'N/A';

        if (schema_has_column('complaint', 'job_id')) {
            $issue->job_id = $jobId;
        }

        if (schema_has_column('complaint', 'quality_control_id')) {
            $issue->quality_control_id = $qualityControlId;
            $issue->quality_control_reason = $reason;
        }

        $issue->save();

        // Backlink the inspection schedule to this issue if the column exists.
        if ($issue->id && class_exists('Modules\\CleanQuality\\Entities\\Schedule') && schema_has_column('inspection_schedules', 'complaint_id')) {
            try {
                $schedule = \Modules\CleanQuality\Entities\Schedule::find($qualityControlId);
                if ($schedule && empty($schedule->complaint_id)) {
                    $schedule->complaint_id = $issue->id;
                    $schedule->save();
                }
            } catch (\Throwable $e) {
                // Non-fatal.
            }
        }
    }
}

/**
 * Helper: safely check for a column without hard-failing if Schema is unavailable.
 */
function schema_has_column(string $table, string $column): bool
{
    try {
        return \Illuminate\Support\Facades\Schema::hasColumn($table, $column);
    } catch (\Throwable $e) {
        return false;
    }
}
