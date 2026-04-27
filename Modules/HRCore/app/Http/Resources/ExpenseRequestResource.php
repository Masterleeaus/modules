<?php

namespace Modules\HRCore\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'expense_number' => $this->expense_number,
            'expense_date' => $this->expense_date ? $this->expense_date->format('Y-m-d') : null,
            'amount' => (float) $this->amount,
            'approved_amount' => $this->approved_amount ? (float) $this->approved_amount : null,
            'currency' => $this->currency ?? 'USD',
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status ? $this->status->value : 'pending',
            'expense_type' => $this->whenLoaded('expenseType', function () {
                return [
                    'id' => $this->expenseType->id,
                    'name' => $this->expenseType->name,
                    'requires_proof' => (bool) $this->expenseType->is_proof_required,
                ];
            }),
            'project_code' => $this->project_code,
            'cost_center' => $this->cost_center,
            'attachments' => $this->attachments ?? [],
            'approved_by' => $this->whenLoaded('approvedBy', function () {
                return [
                    'id' => $this->approvedBy->id,
                    'name' => $this->approvedBy->name,
                ];
            }),
            'approved_at' => $this->approved_at ? $this->approved_at->toIso8601String() : null,
            'approval_remarks' => $this->approval_remarks,
            'rejected_by' => $this->whenLoaded('rejectedBy', function () {
                return [
                    'id' => $this->rejectedBy->id,
                    'name' => $this->rejectedBy->name,
                ];
            }),
            'rejected_at' => $this->rejected_at ? $this->rejected_at->toIso8601String() : null,
            'rejection_reason' => $this->rejection_reason,
            'processed_by' => $this->whenLoaded('processedBy', function () {
                return [
                    'id' => $this->processedBy->id,
                    'name' => $this->processedBy->name,
                ];
            }),
            'processed_at' => $this->processed_at ? $this->processed_at->toIso8601String() : null,
            'payment_reference' => $this->payment_reference,
            'processing_notes' => $this->processing_notes,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
