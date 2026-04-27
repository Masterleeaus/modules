<?php

namespace Modules\HRCore\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompensatoryOffResource extends JsonResource
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
            'worked_on' => $this->worked_date?->format('Y-m-d'),
            'reason' => $this->reason,
            'status' => $this->status,
            'approved_by' => $this->whenLoaded('approvedBy', function () {
                return new ManagerResource($this->approvedBy);
            }),
            'approved_at' => $this->approved_at,
            'rejection_reason' => $this->approval_notes,
            'used_on' => $this->used_date?->format('Y-m-d'),
            'expires_on' => $this->expiry_date?->format('Y-m-d'),
            'supporting_documents' => $this->supporting_documents ?? [],
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
