<?php

namespace Modules\TitanIntegrations\Services\Integrations;

use Illuminate\Support\Facades\Http;
use Modules\TitanIntegrations\Entities\Integration;

/**
 * Stripe — payment processing for cleaning invoices.
 * Uses the secret key stored via getDecryptedApiKey().
 */
class StripeIntegration
{
    private const BASE_URL = 'https://api.stripe.com/v1/';

    public function getProvider(): string { return 'stripe'; }

    public function testConnection(Integration $integration): array
    {
        $response = $this->request($integration, 'GET', 'account');

        if ($response->successful()) {
            return ['ok' => true, 'account' => $response->json('email', 'Stripe')];
        }

        return ['ok' => false, 'error' => $response->json('error.message', 'Stripe connection failed')];
    }

    /**
     * Create a PaymentIntent and return the client_secret for checkout.
     */
    public function createPaymentIntent(Integration $integration, array $invoice): array
    {
        $response = $this->request($integration, 'POST', 'payment_intents', [
            'amount'      => $invoice['amount_cents'],
            'currency'    => strtolower($invoice['currency'] ?? 'aud'),
            'description' => $invoice['description'] ?? 'Cleaning invoice ' . ($invoice['invoice_number'] ?? ''),
            'metadata'    => [
                'invoice_id'     => $invoice['id']             ?? null,
                'invoice_number' => $invoice['invoice_number'] ?? null,
            ],
        ]);

        if ($response->successful()) {
            return [
                'payment_intent_id' => $response->json('id'),
                'client_secret'     => $response->json('client_secret'),
            ];
        }

        return ['error' => $response->json('error.message', 'Failed to create payment intent')];
    }

    /**
     * Charge a stored card (customer's default payment method) on file.
     */
    public function chargeCardOnFile(Integration $integration, string $customerId, int $amountCents, string $description): array
    {
        // Retrieve the customer's default payment method.
        $customer = $this->request($integration, 'GET', "customers/{$customerId}")->json();
        $pm = $customer['invoice_settings']['default_payment_method']
           ?? $customer['default_source']
           ?? null;

        if (!$pm) {
            return ['error' => 'No payment method on file'];
        }

        $response = $this->request($integration, 'POST', 'payment_intents', [
            'amount'               => $amountCents,
            'currency'             => 'aud',
            'customer'             => $customerId,
            'payment_method'       => $pm,
            'description'          => $description,
            'confirm'              => 'true',
            'off_session'          => 'true',
        ]);

        if ($response->successful()) {
            return [
                'payment_intent_id' => $response->json('id'),
                'status'            => $response->json('status'),
            ];
        }

        return ['error' => $response->json('error.message', 'Charge failed')];
    }

    /**
     * Create a Stripe Customer from a client record.
     * Returns the Stripe customer_id.
     */
    public function createCustomer(Integration $integration, array $client): string
    {
        $response = $this->request($integration, 'POST', 'customers', array_filter([
            'email'       => $client['email']        ?? null,
            'name'        => $client['name']          ?? null,
            'phone'       => $client['phone']         ?? null,
            'description' => $client['description']   ?? null,
            'metadata'    => ['client_id' => $client['id'] ?? null],
        ]));

        return $response->json('id', '');
    }

    /**
     * Retrieve the status of a PaymentIntent.
     */
    public function getPaymentStatus(Integration $integration, string $paymentIntentId): string
    {
        $response = $this->request($integration, 'GET', "payment_intents/{$paymentIntentId}");

        return $response->json('status', 'unknown');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function request(Integration $integration, string $method, string $endpoint, array $data = [])
    {
        $key = $integration->getDecryptedApiKey() ?? '';

        $req = Http::withBasicAuth($key, '')->baseUrl(self::BASE_URL);

        return match (strtoupper($method)) {
            'GET'  => $req->get($endpoint, $data),
            'POST' => $req->asForm()->post($endpoint, $data),
            default => $req->get($endpoint),
        };
    }
}
