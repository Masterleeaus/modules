<?php

namespace Modules\TitanIntegrations\Http\Controllers;

use App\Http\Controllers\AccountBaseController;
use Illuminate\Http\Request;
use Modules\TitanIntegrations\Entities\Integration;
use Modules\TitanIntegrations\Services\OAuthService;

class ConnectionController extends AccountBaseController
{
    public function __construct(private OAuthService $oauth)
    {
        parent::__construct();
    }

    /**
     * Step 1: Redirect user to provider's OAuth consent screen.
     *
     * GET /account/titan-integrations/{provider}/oauth/redirect
     */
    public function redirect(string $provider)
    {
        abort_403(user()->permission('manage_integrations') !== 'all');

        $cfg = config("titanintegrations.integrations.{$provider}");
        abort_404(!$cfg || ($cfg['auth'] ?? '') !== 'oauth');

        $url = $this->oauth->buildAuthUrl($provider, company()->id);
        return redirect()->away($url);
    }

    /**
     * Step 2: Handle OAuth callback from provider.
     *
     * GET /account/titan-integrations/{provider}/oauth/callback
     */
    public function callback(Request $request, string $provider)
    {
        $error = $request->input('error');
        if ($error) {
            return redirect()->route('titan-integrations.index')
                ->with('error', "OAuth failed: {$error}");
        }

        $code      = $request->input('code');
        $state     = $request->input('state');
        $stateData = json_decode(base64_decode($state ?? ''), true);
        $companyId = $stateData['company_id'] ?? company()->id;

        $integration = Integration::firstOrNew([
            'company_id' => $companyId,
            'provider'   => $provider,
        ]);
        $integration->is_byo = true;
        $integration->save();

        try {
            $this->oauth->exchangeCode($provider, $code, $integration);

            // Fetch account name for the provider
            $service     = $this->resolveService($provider);
            $integration = $integration->fresh();
            $testResult  = $service ? $service->testConnection($integration) : ['ok' => true, 'account' => null];

            if ($testResult['ok']) {
                $integration->markConnected($testResult['account'] ?? null);

                // For Xero: store tenant ID from connections endpoint
                if ($provider === 'xero') {
                    $this->storeXeroTenantId($integration);
                }

                return redirect()->route('titan-integrations.index')
                    ->with('success', config("titanintegrations.integrations.{$provider}.label") . ' connected successfully!');
            }

            $integration->markError($testResult['error'] ?? 'Post-OAuth test failed');
        } catch (\Throwable $e) {
            $integration->markError($e->getMessage());
        }

        return redirect()->route('titan-integrations.index')
            ->with('error', 'OAuth connection failed — ' . $integration->error_message);
    }

    /**
     * Generate and return an iCal feed URL for the company's bookings.
     *
     * GET /titan-integrations/ical/{company}/{token}
     */
    public function icalFeed(Request $request, int $companyId, string $token)
    {
        $expected = hash_hmac('sha256', "ical_{$companyId}", config('app.key'));
        abort_403(!hash_equals($expected, $token));

        // Query bookings (tasks) for this company
        $tasks = \App\Models\Task::where('company_id', $companyId)
            ->whereNotNull('start_date')
            ->where('start_date', '>=', now()->subDays(30))
            ->where('start_date', '<=', now()->addDays(180))
            ->get();

        $cal  = "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:-//WorkSuite//TitanIntegrations//EN\r\n";
        $cal .= "X-WR-CALNAME:WorkSuite Bookings\r\nX-WR-TIMEZONE:UTC\r\n";

        foreach ($tasks as $task) {
            $uid     = "task-{$task->id}@worksuite";
            $dtstart = \Carbon\Carbon::parse($task->start_date)->format('Ymd\THis\Z');
            $dtend   = \Carbon\Carbon::parse($task->due_date ?? $task->start_date)->addHour()->format('Ymd\THis\Z');
            $cal .= "BEGIN:VEVENT\r\n";
            $cal .= "UID:{$uid}\r\n";
            $cal .= "DTSTART:{$dtstart}\r\n";
            $cal .= "DTEND:{$dtend}\r\n";
            $cal .= "SUMMARY:" . addslashes($task->heading ?? 'Booking') . "\r\n";
            $cal .= "END:VEVENT\r\n";
        }

        $cal .= "END:VCALENDAR\r\n";

        return response($cal, 200, [
            'Content-Type'        => 'text/calendar; charset=UTF-8',
            'Content-Disposition' => 'inline; filename=worksuite-bookings.ics',
        ]);
    }

    private function storeXeroTenantId(Integration $integration): void
    {
        try {
            $response = \Illuminate\Support\Facades\Http::withToken($integration->getDecryptedAccessToken())
                ->get('https://api.xero.com/connections');

            if ($response->successful() && count($response->json()) > 0) {
                $tenantId = $response->json('0.tenantId');
                $settings = $integration->settings ?? [];
                $settings['xero_tenant_id'] = $tenantId;
                $integration->settings = $settings;
                $integration->save();
            }
        } catch (\Throwable) {
            // Non-fatal
        }
    }

    private function resolveService(string $provider): ?object
    {
        return match ($provider) {
            'google_calendar' => app(\Modules\TitanIntegrations\Services\Integrations\GoogleCalendarIntegration::class),
            'xero'            => app(\Modules\TitanIntegrations\Services\Integrations\XeroIntegration::class),
            default           => null,
        };
    }
}
