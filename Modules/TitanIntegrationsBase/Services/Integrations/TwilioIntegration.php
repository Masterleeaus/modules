<?php

namespace Modules\TitanIntegrations\Services\Integrations;

use Illuminate\Support\Facades\Http;
use Modules\TitanIntegrations\Entities\Integration;

/**
 * Twilio — SMS notifications for cleaning jobs (reminders, ETAs, confirmations).
 * Uses account_sid from settings and auth token via getDecryptedApiKey().
 */
class TwilioIntegration
{
    public function getProvider(): string { return 'twilio'; }

    public function testConnection(Integration $integration): array
    {
        $accountSid = $integration->settings['account_sid'] ?? null;
        if (!$accountSid) {
            return ['ok' => false, 'error' => 'No account SID configured'];
        }

        $response = $this->request($integration, $accountSid . '.json');

        if ($response->successful()) {
            return ['ok' => true, 'account' => $response->json('friendly_name', 'Twilio')];
        }

        return ['ok' => false, 'error' => $response->json('message', 'Twilio connection failed')];
    }

    /**
     * Send an SMS message via Twilio.
     * Returns the Twilio Message resource array or an error array.
     */
    public function sendSms(Integration $integration, string $to, string $message): array
    {
        $accountSid = $integration->settings['account_sid'] ?? '';
        $from       = $integration->settings['from_number'] ?? '';

        $response = $this->request($integration, $accountSid . '/Messages.json', [
            'To'   => $to,
            'From' => $from,
            'Body' => $message,
        ]);

        if ($response->successful()) {
            return [
                'sid'    => $response->json('sid'),
                'status' => $response->json('status'),
            ];
        }

        return ['error' => $response->json('message', 'SMS send failed')];
    }

    /**
     * Send a formatted job reminder SMS.
     */
    public function sendJobReminder(Integration $integration, string $to, array $job): array
    {
        $date    = $job['scheduled_date'] ?? 'today';
        $time    = $job['scheduled_time'] ?? '';
        $address = $job['address']        ?? '';
        $ref     = $job['reference']      ?? $job['id'] ?? '';

        $message = "Reminder: Your cleaning service is scheduled for {$date}";
        if ($time)    $message .= " at {$time}";
        if ($address) $message .= ", {$address}";
        if ($ref)     $message .= " (Ref: {$ref})";
        $message .= '. Reply STOP to unsubscribe.';

        return $this->sendSms($integration, $to, $message);
    }

    /**
     * Send an ETA SMS informing the client how many minutes away the cleaner is.
     */
    public function sendEtaSms(Integration $integration, string $to, int $etaMinutes): array
    {
        $message = "Your cleaner is approximately {$etaMinutes} minute(s) away. "
            . 'Please ensure access is available. Reply STOP to unsubscribe.';

        return $this->sendSms($integration, $to, $message);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function request(Integration $integration, string $path, array $data = [])
    {
        $accountSid = $integration->settings['account_sid'] ?? '';
        $authToken  = $integration->getDecryptedApiKey()     ?? '';
        $baseUrl    = 'https://api.twilio.com/2010-04-01/Accounts/';

        $req = Http::withBasicAuth($accountSid, $authToken)->baseUrl($baseUrl);

        return empty($data)
            ? $req->get($path)
            : $req->asForm()->post($path, $data);
    }
}
