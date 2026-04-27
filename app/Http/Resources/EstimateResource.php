<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EstimateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'estimate_number' => $this->estimate_number,
            'status'          => $this->status,
            'subtotal'        => $this->subtotal,
            'tax_total'       => $this->tax_total,
            'total'           => $this->total,
            'valid_until'     => $this->valid_until?->toIso8601String(),
            'sent_at'         => $this->sent_at?->toIso8601String(),
            'accepted_at'     => $this->accepted_at?->toIso8601String(),
            'declined_at'     => $this->declined_at?->toIso8601String(),
            'created_at'      => $this->created_at?->toIso8601String(),
            'updated_at'      => $this->updated_at?->toIso8601String(),
            'customer'        => $this->whenLoaded('customer'),
            'line_items'      => $this->whenLoaded('lineItems'),
        ];
    }
}
