<?php

namespace Modules\HRCore\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BankAccountResource extends JsonResource
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
            'bank_name' => $this->bank_name,
            'account_number' => $this->getMaskedAccountNumber(),
            'account_holder_name' => $this->account_holder_name,
            'branch' => $this->branch,
            'ifsc_code' => $this->ifsc_code,
            'is_primary' => $this->is_primary,
            'is_active' => $this->is_active,
        ];
    }

    /**
     * Get masked account number for security
     */
    private function getMaskedAccountNumber(): string
    {
        $accountNumber = $this->account_number;
        if (strlen($accountNumber) > 4) {
            return '****'.substr($accountNumber, -4);
        }

        return '****';
    }
}
