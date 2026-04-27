<?php

namespace App\Listeners;

use App\Events\EstimateSent;
use App\Mail\EstimateMail;
use App\Services\SmsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEstimateNotification implements ShouldQueue
{
    public function __construct(private readonly SmsService $sms) {}

    public function handle(EstimateSent $event): void
    {
        $estimate = $event->estimate;
        $estimate->loadMissing('customer');

        $this->sendEmail($estimate);
        $this->sendSms($estimate);
    }

    private function sendEmail(\App\Models\Estimate $estimate): void
    {
        if (blank($estimate->customer?->email)) {
            return;
        }

        try {
            Mail::to($estimate->customer->email)->send(new EstimateMail($estimate));
        } catch (\Throwable $e) {
            Log::warning('SendEstimateNotification: email failed', [
                'estimate_id' => $estimate->id,
                'error'       => $e->getMessage(),
            ]);
        }
    }

    private function sendSms(\App\Models\Estimate $estimate): void
    {
        $phone = $estimate->customer?->mobile ?? $estimate->customer?->phone;

        if (blank($phone)) {
            return;
        }

        $url  = route('estimates.public', $estimate->token);
        $name = $estimate->customer->first_name;
        $body = "Hi {$name}, your estimate \"{$estimate->title}\" is ready: {$url}";

        try {
            $this->sms->send($phone, $body);
        } catch (\Throwable $e) {
            Log::warning('SendEstimateNotification: SMS failed', [
                'estimate_id' => $estimate->id,
                'error'       => $e->getMessage(),
            ]);
        }
    }
}
