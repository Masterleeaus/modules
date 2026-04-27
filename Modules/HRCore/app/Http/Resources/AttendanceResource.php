<?php

namespace Modules\HRCore\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
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
            'date' => $this->date?->format('Y-m-d'),
            'check_in_time' => $this->check_in_time,
            'check_out_time' => $this->check_out_time,
            'working_hours' => $this->working_hours,
            'overtime_hours' => $this->overtime_hours,
            'status' => $this->status,
            'shift_id' => $this->shift_id,
            'department_id' => $this->department_id,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
