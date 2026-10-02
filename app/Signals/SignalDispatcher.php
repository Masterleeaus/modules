<?php

namespace App\Signals;

use App\Models\Signal;
use App\Models\SignalDispatchLog;

/**
 * Dispatches an approved Signal to all registered handlers and writes to the
 * dispatch log. Failed dispatches are recorded for dead-letter handling.
 */
class SignalDispatcher
{
    /** @var array<string, list<callable>> */
    private static array $handlers = [];

    /**
     * Register a handler for the given signal type.
     *
     * @param  string    $type    Signal contract identifier (e.g. 'job.created')
     * @param  callable  $handler Receives the Signal instance
     */
    public static function registerHandler(string $type, callable $handler): void
    {
        self::$handlers[$type][] = $handler;
    }

    /**
     * Remove all registered handlers. Useful for resetting state in tests.
     */
    public static function clearHandlers(): void
    {
        self::$handlers = [];
    }

    /**
     * Dispatch the signal to all registered handlers, writing a log entry for
     * each attempt. If any handler fails the signal is marked 'failed'.
     */
    public function dispatch(Signal $signal): void
    {
        $handlers   = self::$handlers[$signal->type] ?? [];
        $allSuccess = true;

        foreach ($handlers as $handler) {
            $handlerName = $this->resolveHandlerName($handler);

            try {
                $handler($signal);

                SignalDispatchLog::create([
                    'signal_id'  => $signal->id,
                    'handler'    => $handlerName,
                    'result'     => SignalDispatchLog::RESULT_SUCCESS,
                    'attempts'   => 1,
                    'created_at' => now(),
                ]);
            } catch (\Throwable $e) {
                $allSuccess = false;

                SignalDispatchLog::create([
                    'signal_id'  => $signal->id,
                    'handler'    => $handlerName,
                    'result'     => SignalDispatchLog::RESULT_FAILURE,
                    'attempts'   => 1,
                    'last_error' => $e->getMessage(),
                    'created_at' => now(),
                ]);
            }
        }

        $signal->update([
            'status'       => $allSuccess ? Signal::STATUS_DISPATCHED : Signal::STATUS_FAILED,
            'processed_at' => now(),
        ]);
    }

    private function resolveHandlerName(callable $handler): string
    {
        if (is_array($handler)) {
            return implode('::', array_map(
                fn ($part) => is_object($part) ? get_class($part) : (string) $part,
                $handler,
            ));
        }

        if (is_object($handler)) {
            return get_class($handler);
        }

        return (string) $handler;
    }
}
