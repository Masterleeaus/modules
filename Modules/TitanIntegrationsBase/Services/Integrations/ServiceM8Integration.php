<?php

namespace Modules\TitanIntegrations\Services\Integrations;

use Illuminate\Support\Facades\Http;
use Modules\TitanIntegrations\Entities\Integration;

/**
 * ServiceM8 — cleaning operations platform.
 * Uses HTTP Basic Auth with api_key from settings.
 */
class ServiceM8Integration
{
    private const BASE_URL = 'https://api.servicem8.com/api_1.0/';

    public function getProvider(): string { return 'servicem8'; }

    public function testConnection(Integration $integration): array
    {
        $response = $this->request($integration, 'GET', 'staff.json');

        if ($response->successful()) {
            return ['ok' => true, 'account' => 'ServiceM8'];
        }

        return ['ok' => false, 'error' => $response->json('errorCode', 'ServiceM8 connection failed')];
    }

    /**
     * Create or update a job in ServiceM8.
     * Returns the ServiceM8 job UUID or null on failure.
     */
    public function syncJob(Integration $integration, array $job): ?string
    {
        $payload = array_filter([
            'status'           => $job['status']           ?? 'Quote',
            'job_address'      => $job['address']          ?? null,
            'job_description'  => $job['description']      ?? null,
            'date'             => $job['scheduled_date']   ?? null,
            'start_time'       => $job['start_time']       ?? null,
            'end_time'         => $job['end_time']         ?? null,
            'generated_job_id' => $job['reference']        ?? null,
            'note'             => $job['notes']            ?? null,
        ]);

        // Update if uuid is present, otherwise create.
        if (!empty($job['sm8_uuid'])) {
            $response = $this->request($integration, 'POST', "job/{$job['sm8_uuid']}.json", $payload);
        } else {
            $response = $this->request($integration, 'POST', 'job.json', $payload);
        }

        if ($response->successful()) {
            $location = $response->header('x-record-uuid');
            return $location ?: $response->json('uuid');
        }

        return null;
    }

    /**
     * Create or update an invoice in ServiceM8.
     * Returns the ServiceM8 invoice UUID or null on failure.
     */
    public function syncInvoice(Integration $integration, array $invoice): ?string
    {
        $payload = array_filter([
            'job_uuid'          => $invoice['sm8_job_uuid']  ?? null,
            'status'            => $invoice['status']        ?? 'Draft',
            'total_amount'      => $invoice['total']         ?? null,
            'invoice_number'    => $invoice['invoice_number'] ?? null,
            'date'              => $invoice['date']           ?? null,
        ]);

        $response = $this->request($integration, 'POST', 'jobinvoice.json', $payload);

        if ($response->successful()) {
            return $response->header('x-record-uuid') ?: $response->json('uuid');
        }

        return null;
    }

    /**
     * Pull jobs from ServiceM8 updated since a given date (YYYY-MM-DD).
     */
    public function pullJobs(Integration $integration, string $from): array
    {
        $response = $this->request($integration, 'GET', 'job.json', [
            '$filter' => "edit_date gt datetime'{$from}T00:00:00'",
        ]);

        return $response->successful() ? ($response->json() ?? []) : [];
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function request(Integration $integration, string $method, string $endpoint, array $data = [])
    {
        $apiKey = $integration->settings['api_key'] ?? '';

        $req = Http::withBasicAuth($apiKey, '')->baseUrl(self::BASE_URL);

        return match (strtoupper($method)) {
            'POST' => $req->asForm()->post($endpoint, $data),
            default => $req->get($endpoint, $data),
        };
    }
}
