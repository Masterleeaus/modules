<?php

namespace Modules\TitanIntegrations\Services\Integrations;

use Illuminate\Support\Facades\Http;
use Modules\TitanIntegrations\Entities\Integration;
use Modules\TitanIntegrations\Services\OAuthService;

/**
 * Google Sheets — export WorkSuite data to a Google Spreadsheet.
 */
class GoogleSheetsIntegration
{
    private const SHEETS_BASE = 'https://sheets.googleapis.com/v4/spreadsheets';

    public function __construct(protected OAuthService $oauth) {}

    public function getProvider(): string { return 'google_sheets'; }

    public function testConnection(Integration $integration): array
    {
        $token       = $this->getValidToken($integration);
        $spreadsheetId = $integration->settings['spreadsheet_id'] ?? null;

        if (!$spreadsheetId) {
            return ['ok' => true, 'account' => 'Google Sheets (no spreadsheet selected yet)'];
        }

        $response = Http::withToken($token)
            ->get(self::SHEETS_BASE . "/{$spreadsheetId}");

        return $response->successful()
            ? ['ok' => true, 'account' => $response->json('properties.title', 'Google Sheets')]
            : ['ok' => false, 'error' => $response->json('error.message', 'Connection failed')];
    }

    /**
     * Append rows to a sheet.
     *
     * @param array $rows  e.g. [['2024-01-01', 'Booking #1', '$120'], ...]
     */
    public function appendRows(Integration $integration, string $range, array $rows): bool
    {
        $token         = $this->getValidToken($integration);
        $spreadsheetId = $integration->settings['spreadsheet_id'] ?? null;
        if (!$spreadsheetId) return false;

        $response = Http::withToken($token)
            ->post(self::SHEETS_BASE . "/{$spreadsheetId}/values/{$range}:append?valueInputOption=USER_ENTERED", [
                'values' => $rows,
            ]);

        return $response->successful();
    }

    /**
     * Clear and rewrite a sheet range (full export).
     */
    public function writeRange(Integration $integration, string $range, array $rows): bool
    {
        $token         = $this->getValidToken($integration);
        $spreadsheetId = $integration->settings['spreadsheet_id'] ?? null;
        if (!$spreadsheetId) return false;

        $response = Http::withToken($token)
            ->put(self::SHEETS_BASE . "/{$spreadsheetId}/values/{$range}?valueInputOption=USER_ENTERED", [
                'range'  => $range,
                'values' => $rows,
            ]);

        return $response->successful();
    }

    private function getValidToken(Integration $integration): string
    {
        if ($integration->isTokenExpired()) {
            $this->oauth->refreshToken($integration);
            $integration = $integration->fresh();
        }
        return $integration->getDecryptedAccessToken() ?? '';
    }
}
