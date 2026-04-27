<?php

namespace Modules\HRCore\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaveTypeResource extends JsonResource
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
            'name' => $this->name,
            'code' => $this->code,
            'is_proof_required' => $this->is_proof_required,
            'allow_carry_forward' => $this->allow_carry_forward,
            'max_carry_forward' => $this->max_carry_forward,
            'allow_encashment' => $this->allow_encashment,
            'status' => $this->status,
            'is_accrual_enabled' => $this->is_accrual_enabled,
            'accrual_rate' => $this->accrual_rate,
            'is_comp_off_type' => $this->is_comp_off_type,
        ];
    }
}
