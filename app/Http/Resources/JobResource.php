<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'title'             => $this->title,
            'status'            => $this->status,
            'description'       => $this->description,
            'technician_notes'  => $this->technician_notes,
            'customer_notes'    => $this->customer_notes,
            'scheduled_at'      => $this->scheduled_at?->toIso8601String(),
            'arrived_at'        => $this->arrived_at?->toIso8601String(),
            'started_at'        => $this->started_at?->toIso8601String(),
            'completed_at'      => $this->completed_at?->toIso8601String(),
            'cancelled_at'      => $this->cancelled_at?->toIso8601String(),
            'created_at'        => $this->created_at?->toIso8601String(),
            'updated_at'        => $this->updated_at?->toIso8601String(),
            'customer'          => $this->whenLoaded('customer'),
            'property'          => $this->whenLoaded('property'),
            'job_type'          => $this->whenLoaded('jobType'),
            'checklist_items'   => $this->whenLoaded('checklistItems'),
            'attachments'       => $this->whenLoaded('attachments'),
            'line_items'        => $this->whenLoaded('lineItems'),
            'messages'          => $this->whenLoaded('messages'),
        ];
    }
}
