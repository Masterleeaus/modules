<?php

namespace Modules\HRCore\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeHistoryResource extends JsonResource
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
            'type' => $this->type,
            'effective_date' => $this->effective_date,
            'from_designation' => $this->from_designation,
            'to_designation' => $this->to_designation,
            'from_department' => $this->from_department,
            'to_department' => $this->to_department,
            'from_salary' => $this->from_salary,
            'to_salary' => $this->to_salary,
            'reason' => $this->reason,
            'remarks' => $this->remarks,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
