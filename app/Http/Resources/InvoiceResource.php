<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'invoice_number' => $this->invoice_number,
            'status'         => $this->status,
            'subtotal'       => $this->subtotal,
            'tax_total'      => $this->tax_total,
            'total'          => $this->total,
            'amount_paid'    => $this->amount_paid,
            'balance_due'    => $this->balance_due,
            'due_date'       => $this->due_date?->toIso8601String(),
            'issued_at'      => $this->issued_at?->toIso8601String(),
            'paid_at'        => $this->paid_at?->toIso8601String(),
            'voided_at'      => $this->voided_at?->toIso8601String(),
            'created_at'     => $this->created_at?->toIso8601String(),
            'updated_at'     => $this->updated_at?->toIso8601String(),
            'customer'       => $this->whenLoaded('customer'),
            'line_items'     => $this->whenLoaded('lineItems'),
            'payments'       => $this->whenLoaded('payments'),
        ];
    }
}
