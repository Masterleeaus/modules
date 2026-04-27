<?php

namespace Modules\HRCore\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaveRequestResource extends JsonResource
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
            'leave_type' => $this->whenLoaded('leaveType', function () {
                return new LeaveTypeResource($this->leaveType);
            }),
            'from_date' => $this->from_date,
            'to_date' => $this->to_date,
            'total_days' => $this->total_days,
            'is_half_day' => $this->is_half_day,
            'half_day_type' => $this->half_day_type,
            'reason' => $this->user_notes,
            'status' => $this->status,
            'approved_by' => $this->whenLoaded('approvedBy', function () {
                return new ManagerResource($this->approvedBy);
            }),
            'approved_at' => $this->approved_at,
            'approval_notes' => $this->approval_notes,
            'emergency_contact' => $this->emergency_contact,
            'emergency_phone' => $this->emergency_phone,
            'is_abroad' => $this->is_abroad,
            'abroad_location' => $this->abroad_location,
            'document' => $this->document,
            'document_url' => $this->getLeaveDocumentUrl(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
