<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Signal Approval Rules
    |--------------------------------------------------------------------------
    |
    | Maps signal types to governance decisions. Supported values:
    |   'auto_approve'   — signal proceeds to dispatch immediately (default)
    |   'require_review' — signal is held as 'pending' for human or AI approval
    |   'auto_reject'    — signal is refused without dispatch
    |
    | A 'default' key acts as a catch-all for any unspecified signal type.
    |
    */

    'approval_rules' => [
        'default' => 'auto_approve',

        // Examples of signals that require explicit approval before dispatch:
        // 'invoice.voided'    => 'require_review',
        // 'payment.refunded'  => 'require_review',
    ],

    /*
    |--------------------------------------------------------------------------
    | Dead-Letter Queue Settings
    |--------------------------------------------------------------------------
    |
    | Controls behaviour when signal dispatch fails.
    |
    */

    'dead_letter' => [
        'enabled'     => true,
        'max_retries' => 3,
    ],

];
