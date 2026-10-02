<?php

namespace App\Signals;

use App\Models\Signal;

/**
 * Enforces approval rules for signals before they are dispatched.
 *
 * Rules are loaded from config('signals.approval_rules') and support three
 * outcomes:
 *   - 'approved'  — signal may proceed to dispatch immediately
 *   - 'pending'   — signal must wait for human or AI approval
 *   - 'rejected'  — signal is refused and will not be dispatched
 *
 * The config key is the signal type (e.g. 'payment.refunded'); a 'default'
 * key acts as a catch-all. If no rule is found the signal is auto-approved.
 */
class SignalGovernor
{
    /**
     * Evaluate the governance rules for the signal and return the decision.
     *
     * @return 'approved'|'pending'|'rejected'
     */
    public function evaluate(Signal $signal): string
    {
        /** @var array<string, string> $rules */
        $rules = config('signals.approval_rules', []);

        $rule = $rules[$signal->type] ?? $rules['default'] ?? 'auto_approve';

        return match ($rule) {
            'auto_approve'  => 'approved',
            'require_review' => 'pending',
            'auto_reject'   => 'rejected',
            default          => 'approved',
        };
    }
}
