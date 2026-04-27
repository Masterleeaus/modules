<?php

namespace Modules\HRCore\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseTypeResource extends JsonResource
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
            'description' => $this->description,
            'requires_proof' => (bool) $this->requires_receipt,
            'max_amount' => $this->max_amount ? (float) $this->max_amount : null,
            'is_active' => $this->status === 'active' || $this->status === \App\Enums\Status::ACTIVE,
        ];
    }
}
