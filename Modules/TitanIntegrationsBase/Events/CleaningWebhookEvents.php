<?php

namespace Modules\TitanIntegrations\Events;

/**
 * Cleaning-specific webhook event type constants.
 *
 * Usage:
 *   $dispatcher->dispatchCleaningEvent(CleaningWebhookEvents::JOB_CREATED, $payload, $companyId);
 */
class CleaningWebhookEvents
{
    const JOB_CREATED       = 'job.created';
    const JOB_COMPLETED     = 'job.completed';
    const PAYMENT_RECEIVED  = 'payment.received';
    const REVIEW_SUBMITTED  = 'review.submitted';
    const NEW_BOOKING       = 'new_booking';
    const INVOICE_PAID      = 'invoice.paid';
    const STAFF_CLOCKED_IN  = 'staff.clocked_in';
    const STAFF_CLOCKED_OUT = 'staff.clocked_out';

    /**
     * All registered cleaning event types.
     */
    public static function all(): array
    {
        return [
            self::JOB_CREATED,
            self::JOB_COMPLETED,
            self::PAYMENT_RECEIVED,
            self::REVIEW_SUBMITTED,
            self::NEW_BOOKING,
            self::INVOICE_PAID,
            self::STAFF_CLOCKED_IN,
            self::STAFF_CLOCKED_OUT,
        ];
    }
}
