<?php

namespace App\Workflow;

use App\Models\WorkflowTransition;
use App\Workflow\Exceptions\InvalidTransitionException;
use App\Workflow\Guards\GuardInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class StateMachine
{
    /** @var array<string,mixed> */
    private array $definition;

    public function __construct(private readonly string $entityType)
    {
        $this->definition = config("workflows.{$entityType}", []);

        if (empty($this->definition)) {
            throw new \InvalidArgumentException(
                "No workflow definition found for entity type '{$entityType}'."
            );
        }
    }

    // -------------------------------------------------------------------------
    // Public API
    // -------------------------------------------------------------------------

    /**
     * Determine whether the given transition can legally be applied to the entity.
     * Does NOT run guards — use canWithGuards() for a full check.
     */
    public function can(Model $entity, string $transition): bool
    {
        $def = $this->transitionDefinition($transition);

        if ($def === null) {
            return false;
        }

        $from = (array) $def['from'];

        return in_array($this->currentState($entity), $from, true);
    }

    /**
     * Determine whether the transition is legal AND all guards pass.
     *
     * @param  array<string,mixed>  $context
     */
    public function canWithGuards(Model $entity, string $transition, array $context = []): bool
    {
        if (! $this->can($entity, $transition)) {
            return false;
        }

        foreach ($this->guardsFor($transition) as $guard) {
            if (! $guard->check($entity, $transition, $context)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Apply the transition to the entity, recording history.
     *
     * @param  array<string,mixed>  $context
     *
     * @throws InvalidTransitionException  When the transition is not legal.
     * @throws \RuntimeException           When a guard blocks the transition.
     */
    public function apply(Model $entity, string $transition, array $context = []): void
    {
        $fromState = $this->currentState($entity);

        if (! $this->can($entity, $transition)) {
            throw new InvalidTransitionException($fromState, $transition);
        }

        foreach ($this->guardsFor($transition) as $guard) {
            if (! $guard->check($entity, $transition, $context)) {
                throw new \RuntimeException($guard->message());
            }
        }

        $def     = $this->transitionDefinition($transition);
        $toState = $def['to'];

        $entity->setAttribute('status', $toState);
        $entity->save();

        $this->recordTransition($entity, $fromState, $toState, $transition, $context);
    }

    /**
     * Return the current state of the entity (the value of its `status` attribute).
     */
    public function currentState(Model $entity): string
    {
        return (string) $entity->getAttribute('status');
    }

    /**
     * Return all transition names that are currently applicable to the entity
     * (only structural legality — guards are NOT checked).
     *
     * @return array<string>
     */
    public function availableTransitions(Model $entity): array
    {
        $current = $this->currentState($entity);

        return collect($this->definition['transitions'] ?? [])
            ->filter(function (array $def) use ($current) {
                return in_array($current, (array) $def['from'], true);
            })
            ->keys()
            ->values()
            ->all();
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /** @return array<string,mixed>|null */
    private function transitionDefinition(string $transition): ?array
    {
        return $this->definition['transitions'][$transition] ?? null;
    }

    /**
     * Instantiate guard objects for the given transition.
     *
     * @return array<GuardInterface>
     */
    private function guardsFor(string $transition): array
    {
        $classes = $this->definition['guards'][$transition] ?? [];

        return array_map(fn (string $class) => app($class), $classes);
    }

    private function recordTransition(
        Model $entity,
        string $fromState,
        string $toState,
        string $transition,
        array $context,
    ): void {
        WorkflowTransition::create([
            'organization_id' => $entity->getAttribute('organization_id'),
            'entity_type'     => $this->entityType,
            'entity_id'       => (string) $entity->getKey(),
            'from_state'      => $fromState,
            'to_state'        => $toState,
            'transition'      => $transition,
            'triggered_by'    => Auth::id(),
            'context'         => empty($context) ? null : $context,
        ]);
    }
}
