<?php

namespace Modules\TitanIntegrations\Services\Integrations;

use Illuminate\Support\Facades\Http;
use Modules\TitanIntegrations\Entities\Integration;

/**
 * Deputy — AU workforce management: staff scheduling, timesheets, payroll.
 * Uses subdomain-based URL and Bearer token authentication.
 */
class DeputyIntegration
{
    public function getProvider(): string { return 'deputy'; }

    public function testConnection(Integration $integration): array
    {
        $response = Http::withToken($this->getToken($integration))
            ->get($this->baseUrl($integration) . '/resource/Company/Info');

        if ($response->successful()) {
            $name = $response->json('CompanyName', 'Deputy');
            return ['ok' => true, 'account' => $name];
        }

        return ['ok' => false, 'error' => $response->json('error', 'Deputy connection failed')];
    }

    /**
     * Push timesheet hours/dates to Deputy as a Timesheet record.
     * Returns the created Timesheet ID or null on failure.
     */
    public function syncTimesheet(Integration $integration, array $timesheet): ?string
    {
        $response = Http::withToken($this->getToken($integration))
            ->post($this->baseUrl($integration) . '/resource/Timesheet', [
                'EmployeeId' => $timesheet['employee_id'],
                'Date'       => $timesheet['date'],
                'StartTime'  => $timesheet['start_time'] ?? null,
                'EndTime'    => $timesheet['end_time']   ?? null,
                'TotalTime'  => $timesheet['total_hours'] ?? null,
                'Comment'    => $timesheet['notes'] ?? null,
            ]);

        return $response->successful() ? (string) $response->json('Id') : null;
    }

    /**
     * Create or update an employee in Deputy.
     * Returns the Deputy employee ID or null on failure.
     */
    public function syncStaff(Integration $integration, array $staff): ?string
    {
        $payload = array_filter([
            'FirstName'   => $staff['first_name'] ?? null,
            'LastName'    => $staff['last_name']  ?? null,
            'Email'       => $staff['email']      ?? null,
            'Phone'       => $staff['phone']      ?? null,
            'Active'      => $staff['active']     ?? true,
        ]);

        // Update if deputy_id is provided, otherwise create.
        if (!empty($staff['deputy_id'])) {
            $response = Http::withToken($this->getToken($integration))
                ->post($this->baseUrl($integration) . '/resource/Employee/' . $staff['deputy_id'], $payload);
        } else {
            $response = Http::withToken($this->getToken($integration))
                ->post($this->baseUrl($integration) . '/resource/Employee', $payload);
        }

        return $response->successful() ? (string) $response->json('Id') : null;
    }

    /**
     * Fetch the day's schedule from Deputy for a given date (YYYY-MM-DD).
     */
    public function getSchedule(Integration $integration, string $date): array
    {
        $response = Http::withToken($this->getToken($integration))
            ->get($this->baseUrl($integration) . '/resource/Roster/QUERY', [
                'search' => [
                    'Date' => ['field' => 'Date', 'type' => 'eq', 'data' => $date],
                ],
            ]);

        if ($response->successful()) {
            return $response->json() ?? [];
        }

        return [];
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function baseUrl(Integration $integration): string
    {
        $subdomain = $integration->settings['subdomain'] ?? '';
        return "https://{$subdomain}.deputy.com/api/v1";
    }

    private function getToken(Integration $integration): string
    {
        return $integration->getDecryptedApiKey() ?? '';
    }
}
