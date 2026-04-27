<?php

namespace App\Workflow\Exceptions;

use RuntimeException;

class InvalidTransitionException extends RuntimeException
{
    public function __construct(
        public readonly string $fromState,
        public readonly string $transition,
        string $message = '',
    ) {
        parent::__construct(
            $message ?: "Cannot apply transition '{$transition}' from state '{$fromState}'."
        );
    }
}
