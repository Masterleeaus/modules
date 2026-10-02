<?php

namespace App\Workflow\Guards;

use Illuminate\Database\Eloquent\Model;

interface GuardInterface
{
    /**
     * Return true if the transition is allowed to proceed.
     *
     * @param  Model  $entity     The entity being transitioned.
     * @param  string  $transition The transition name (e.g. 'complete').
     * @param  array<string,mixed>  $context  Arbitrary caller-supplied context.
     */
    public function check(Model $entity, string $transition, array $context = []): bool;

    /**
     * Human-readable reason why the guard blocked the transition.
     * Only called when check() returns false.
     */
    public function message(): string;
}
