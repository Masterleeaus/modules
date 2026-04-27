<?php

namespace Modules\TitanGo\Http\Controllers;

use App\Events\JobStatusChanged;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Job;
use App\Models\JobChecklistItem;
use App\Models\JobLineItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TechnicianSyncController extends Controller
{
    /**
     * Apply a batch of offline mutations in one request.
     *
     * Payload shape:
     * {
     *   "mutations": [
     *     { "type": "status",    "job_id": 1, "status": "in_progress" },
     *     { "type": "notes",     "job_id": 1, "technician_notes": "…" },
     *     { "type": "checklist", "job_id": 1, "item_id": 5, "completed": true },
     *     { "type": "line_item_create", "job_id": 1, "name": "…", "unit_price": 0, "quantity": 1 },
     *     { "type": "line_item_update", "job_id": 1, "line_item_id": 3, "quantity": 2 },
     *     { "type": "line_item_delete", "job_id": 1, "line_item_id": 3 }
     *   ]
     * }
     *
     * Each mutation is applied in order. Partial success is reported per-item.
     */
    public function batch(Request $request): JsonResponse
    {
        $request->validate([
            'mutations'          => ['required', 'array', 'min:1', 'max:200'],
            'mutations.*.type'   => ['required', 'string'],
            'mutations.*.job_id' => ['required', 'integer'],
        ]);

        $userId  = $request->user()->id;
        $results = [];

        foreach ($request->input('mutations') as $index => $mutation) {
            try {
                $results[] = $this->applyMutation($mutation, $userId);
            } catch (\Throwable $e) {
                $results[] = [
                    'index'  => $index,
                    'type'   => $mutation['type'] ?? 'unknown',
                    'job_id' => $mutation['job_id'] ?? null,
                    'status' => 'error',
                    'error'  => $e->getMessage(),
                ];
            }
        }

        $hasErrors = collect($results)->contains('status', 'error');

        return ApiResponse::success([
            'status'  => $hasErrors ? 'partial' : 'ok',
            'results' => $results,
        ], [], $hasErrors ? 207 : 200);
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function applyMutation(array $mutation, int $userId): array
    {
        $type  = $mutation['type'];
        $jobId = $mutation['job_id'];

        $job = Job::findOrFail($jobId);
        abort_unless($job->assigned_to === $userId, 403);

        return match ($type) {
            'status'            => $this->applyStatus($job, $mutation),
            'notes'             => $this->applyNotes($job, $mutation),
            'customer_notes'    => $this->applyCustomerNotes($job, $mutation),
            'checklist'         => $this->applyChecklist($job, $mutation),
            'line_item_create'  => $this->applyLineItemCreate($job, $mutation),
            'line_item_update'  => $this->applyLineItemUpdate($job, $mutation),
            'line_item_delete'  => $this->applyLineItemDelete($job, $mutation),
            default             => throw new \InvalidArgumentException("Unknown mutation type: {$type}"),
        };
    }

    private function applyStatus(Job $job, array $mutation): array
    {
        $allowedStatuses = array_diff(array_keys(Job::statuses()), [Job::STATUS_CANCELLED]);

        $status = $mutation['status'] ?? null;
        if (! $status || ! in_array($status, $allowedStatuses, true)) {
            throw new \InvalidArgumentException('Invalid status value.');
        }

        $timestamps = match ($status) {
            Job::STATUS_IN_PROGRESS => [
                'arrived_at' => $job->arrived_at ?? now(),
                'started_at' => $job->started_at ?? now(),
            ],
            Job::STATUS_COMPLETED => ['completed_at' => now()],
            default => [],
        };

        $oldStatus = $job->status;
        $job->update(['status' => $status, ...$timestamps]);

        JobStatusChanged::dispatch($job->fresh(), $oldStatus, $status);

        return ['type' => 'status', 'job_id' => $job->id, 'status' => 'ok'];
    }

    private function applyNotes(Job $job, array $mutation): array
    {
        $job->update(['technician_notes' => $mutation['technician_notes'] ?? null]);

        return ['type' => 'notes', 'job_id' => $job->id, 'status' => 'ok'];
    }

    private function applyCustomerNotes(Job $job, array $mutation): array
    {
        $job->update(['customer_notes' => $mutation['customer_notes'] ?? null]);

        return ['type' => 'customer_notes', 'job_id' => $job->id, 'status' => 'ok'];
    }

    private function applyChecklist(Job $job, array $mutation): array
    {
        $item = JobChecklistItem::where('job_id', $job->id)
            ->findOrFail($mutation['item_id'] ?? 0);

        $completed = filter_var($mutation['completed'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $item->update(['completed_at' => $completed ? now() : null]);

        return ['type' => 'checklist', 'job_id' => $job->id, 'item_id' => $item->id, 'status' => 'ok'];
    }

    private function applyLineItemCreate(Job $job, array $mutation): array
    {
        $data = [
            'name'       => $mutation['name'] ?? '',
            'unit_price' => $mutation['unit_price'] ?? 0,
            'quantity'   => $mutation['quantity'] ?? 1,
            'sort_order' => $job->lineItems()->max('sort_order') + 1,
        ];

        if (! empty($mutation['item_id'])) {
            $data['item_id'] = $mutation['item_id'];
        }

        $lineItem = $job->lineItems()->create($data);

        return ['type' => 'line_item_create', 'job_id' => $job->id, 'line_item_id' => $lineItem->id, 'status' => 'ok'];
    }

    private function applyLineItemUpdate(Job $job, array $mutation): array
    {
        $lineItem = JobLineItem::where('job_id', $job->id)
            ->findOrFail($mutation['line_item_id'] ?? 0);

        $data = array_filter([
            'name'       => $mutation['name'] ?? null,
            'unit_price' => $mutation['unit_price'] ?? null,
            'quantity'   => $mutation['quantity'] ?? null,
        ], fn ($v) => $v !== null);

        $lineItem->update($data);

        return ['type' => 'line_item_update', 'job_id' => $job->id, 'line_item_id' => $lineItem->id, 'status' => 'ok'];
    }

    private function applyLineItemDelete(Job $job, array $mutation): array
    {
        $lineItem = JobLineItem::where('job_id', $job->id)
            ->findOrFail($mutation['line_item_id'] ?? 0);

        $lineItem->delete();

        return ['type' => 'line_item_delete', 'job_id' => $job->id, 'line_item_id' => $mutation['line_item_id'], 'status' => 'ok'];
    }
}
