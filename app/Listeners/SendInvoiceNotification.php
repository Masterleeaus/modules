<?php

namespace App\Listeners;

use App\Events\InvoiceSent;
use App\Mail\InvoiceMail;
use App\Services\SmsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendInvoiceNotification implements ShouldQueue
{
    public function __construct(private readonly SmsService $sms) {}

    public function handle(InvoiceSent $event): void
    {
        $invoice = $event->invoice;
        $invoice->loadMissing('customer');

        $customer = $invoice->customer;

        if (! $customer) {
            return;
        }

        // Send email notification
        if (filled($customer->email)) {
            Mail::to($customer->email)->queue(new InvoiceMail($invoice));
        }

        // Send SMS notification if a mobile or phone number is on file
        $phone = $customer->mobile ?? $customer->phone;

        if (filled($phone)) {
            $body = "Hi {$customer->first_name}, your invoice {$invoice->invoice_number} "
                . 'for $' . number_format((float) $invoice->balance_due, 2)
                . ' is ready. Please contact us to arrange payment.';

            try {
                $this->sms->send($phone, $body);
            } catch (\Throwable $e) {
                Log::warning("Invoice SMS notification failed for invoice {$invoice->id}: {$e->getMessage()}");
            }
        }
    }
}
