<?php

namespace App\Signals;

use App\Models\Signal;

/**
 * Entry point for the Signal Engine.
 *
 * Receives a raw signal payload, persists it, runs it through validation and
 * governance, and — when approved — hands it to SignalDispatcher for delivery
 * to registered handlers.
 *
 * Supported sources: 'internal', 'webhook', 'ai', 'device'
 */
class SignalRouter
{
    public function __construct(
        private readonly SignalValidator  $validator,
        private readonly SignalGovernor   $governor,
        private readonly SignalDispatcher $dispatcher,
    ) {}

    /**
     * Route a raw signal payload through the full intake pipeline.
     *
     * @param  string               $source         Signal origin (e.g. 'internal', 'webhook')
     * @param  string               $type           Signal contract identifier (e.g. 'job.created')
     * @param  array<string, mixed> $payload        Signal data
     * @param  int|null             $organizationId Owning organisation (null for platform-level signals)
     */
    public function route(
        string $source,
        string $type,
        array $payload,
        ?int $organizationId = null,
    ): Signal {
        // 1. Persist the incoming signal as 'pending'
        $signal = Signal::create([
            'source'          => $source,
            'type'            => $type,
            'payload'         => $payload,
            'organization_id' => $organizationId,
            'status'          => Signal::STATUS_PENDING,
            'created_at'      => now(),
        ]);

        // 2. Validate payload against the registered contract
        $errors = $this->validator->validate($signal);

        if (! empty($errors)) {
            $signal->update([
                'status'       => Signal::STATUS_FAILED,
                'processed_at' => now(),
            ]);

            return $signal->refresh();
        }

        // 3. Apply governance / approval rules
        $decision = $this->governor->evaluate($signal);

        if ($decision === 'rejected') {
            $signal->update([
                'status'       => Signal::STATUS_REJECTED,
                'processed_at' => now(),
            ]);

            return $signal->refresh();
        }

        if ($decision === 'approved') {
            $signal->update(['status' => Signal::STATUS_APPROVED]);

            // 4. Dispatch to registered handlers
            $this->dispatcher->dispatch($signal);
        }

        // If decision === 'pending', leave the record for human/AI approval

        return $signal->refresh();
    }
}
