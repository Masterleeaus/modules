<?php

namespace App\Signals;

use App\Models\Signal;

/**
 * Validates a Signal's payload against its registered contract.
 *
 * Validation rules:
 *   - The signal type must be registered in SignalRegistry.
 *   - All `required_fields` declared in the contract must be present and
 *     non-null in the payload.
 */
class SignalValidator
{
    public function __construct(private readonly SignalRegistry $registry) {}

    /**
     * Validate the signal payload against its contract.
     *
     * @return list<string>  Empty list means the payload is valid.
     */
    public function validate(Signal $signal): array
    {
        $contract = $this->registry->getContract($signal->type);

        if ($contract === null) {
            return ["Unknown signal type: {$signal->type}"];
        }

        $errors         = [];
        $requiredFields = $contract['required_fields'] ?? [];
        $payload        = $signal->payload ?? [];

        foreach ($requiredFields as $field) {
            if (! array_key_exists($field, $payload) || $payload[$field] === null) {
                $errors[] = "Missing required payload field: {$field}";
            }
        }

        return $errors;
    }
}
