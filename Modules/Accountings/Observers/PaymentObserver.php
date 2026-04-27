<?php

namespace Modules\Accountings\Observers;

use Illuminate\Support\Facades\Log;
use Modules\Accountings\Services\JournalAutoCreationService;

/**
 * Observes Payment model creation to auto-generate journal entries.
 */
class PaymentObserver
{
    public function __construct(private JournalAutoCreationService $journalService) {}

    /**
     * Handle the Payment "created" event.
     */
    public function created(mixed $payment): void
    {
        try {
            $this->journalService->onPaymentReceived($payment);
        } catch (\Throwable $e) {
            Log::warning('[PaymentObserver] Journal auto-creation failed', [
                'payment_id' => $payment->id ?? null,
                'error'      => $e->getMessage(),
            ]);
        }
    }
}
