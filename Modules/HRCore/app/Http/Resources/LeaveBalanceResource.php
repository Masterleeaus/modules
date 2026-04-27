<?php

namespace Modules\HRCore\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaveBalanceResource extends JsonResource
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
            'entitled_days' => $this->entitled_leaves ?? 0,
            'available_days' => $this->available_leaves ?? 0,
            'used_days' => $this->used_leaves ?? 0,
            'pending_days' => $this->pending_days ?? 0,
            'carry_forward_days' => $this->carried_forward_leaves ?? 0,
            'year' => $this->year,
        ];
    }
}
