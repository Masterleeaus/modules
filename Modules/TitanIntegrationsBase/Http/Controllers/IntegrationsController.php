<?php

namespace Modules\TitanIntegrations\Http\Controllers;

use App\Http\Controllers\AccountBaseController;
use Illuminate\Http\Request;
use Modules\TitanIntegrations\Entities\Integration;
use Modules\TitanIntegrations\Entities\IntegrationLog;

class IntegrationsController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'Integrations';
    }

    /**
     * Integration dashboard — shows all available integrations and their connection status.
     */
    public function index()
    {
        $viewPermission = user()->permission('view_integrations');
        abort_403(!in_array($viewPermission, ['all', 'added', 'owned', 'both']));

        $companyId = company()->id;
        $registry  = config('titanintegrations.integrations', []);

        // Load all stored connections for this company
        $connected = Integration::forCompany($companyId)
            ->get()
            ->keyBy('provider');

        // Build display list grouped by category
        $integrations = collect($registry)->map(function ($cfg, $provider) use ($connected) {
            $integration = $connected->get($provider);
            return [
                'provider'     => $provider,
                'label'        => $cfg['label'],
                'icon'         => $cfg['icon'] ?? 'link',
                'category'     => $cfg['category'],
                'auth'         => $cfg['auth'],
                'note'         => $cfg['note'] ?? null,
                'status'       => $integration?->status ?? 'disconnected',
                'is_byo'       => $integration?->is_byo ?? false,
                'last_synced'  => $integration?->last_synced_at?->diffForHumans(),
                'account_name' => $integration?->settings['account_name'] ?? null,
                'error'        => $integration?->error_message,
            ];
        })->groupBy('category');

        $this->data['integrations'] = $integrations;
        $this->data['categories']   = [
            'calendar'      => 'Calendar & Scheduling',
            'accounting'    => 'Accounting',
            'crm'           => 'CRM',
            'marketing'     => 'Marketing',
            'communication' => 'Communication',
            'automation'    => 'Automation',
            'data'          => 'Data & Reporting',
            'publishing'    => 'Publishing',
            'field_service' => 'Field Service',
        ];

        return view('titanintegrations::index', $this->data);
    }

    /**
     * Show connect form for an API-key or webhook integration.
     */
    public function showConnect(string $provider)
    {
        abort_403(user()->permission('manage_integrations') !== 'all');

        $cfg = config("titanintegrations.integrations.{$provider}");
        abort_404(!$cfg);

        $this->data['provider']    = $provider;
        $this->data['config']      = $cfg;
        $this->data['integration'] = Integration::forCompany(company()->id)
            ->where('provider', $provider)
            ->first();

        return view('titanintegrations::connect', $this->data);
    }

    /**
     * Save API key / webhook URL for non-OAuth integrations.
     */
    public function connect(Request $request, string $provider)
    {
        abort_403(user()->permission('manage_integrations') !== 'all');

        $cfg = config("titanintegrations.integrations.{$provider}");
        abort_404(!$cfg);

        $companyId = company()->id;
        $authType  = $cfg['auth'];

        $integration = Integration::firstOrNew([
            'company_id' => $companyId,
            'provider'   => $provider,
        ]);

        $integration->credential_type = $authType;
        $integration->is_byo          = true;

        if ($authType === 'api_key') {
            $integration->api_key = $request->input('api_key');
            // Extra settings (list_id, etc.)
            $settings = $integration->settings ?? [];
            foreach (['list_id', 'base_id', 'site_url', 'username'] as $field) {
                if ($request->filled($field)) {
                    $settings[$field] = $request->input($field);
                }
            }
            $integration->settings = $settings;
        } elseif ($authType === 'webhook') {
            $integration->webhook_url = $request->input('webhook_url');
        }

        $integration->save();

        // Test the connection
        $service = $this->resolveService($provider);
        if ($service) {
            $result = $service->testConnection($integration);
            if ($result['ok']) {
                $integration->markConnected($result['account'] ?? null);
                return response()->json(['ok' => true, 'message' => "Connected to {$cfg['label']} successfully"]);
            } else {
                $integration->markError($result['error'] ?? 'Connection test failed');
                return response()->json(['ok' => false, 'error' => $result['error']], 422);
            }
        }

        $integration->update(['status' => 'connected']);
        return response()->json(['ok' => true]);
    }

    /**
     * Disconnect an integration — removes credentials.
     */
    public function disconnect(string $provider)
    {
        abort_403(user()->permission('manage_integrations') !== 'all');

        Integration::forCompany(company()->id)
            ->where('provider', $provider)
            ->update([
                'status'        => 'disconnected',
                'access_token'  => null,
                'refresh_token' => null,
                'api_key'       => null,
                'webhook_url'   => null,
                'error_message' => null,
            ]);

        return response()->json(['ok' => true, 'message' => 'Integration disconnected']);
    }

    /**
     * Integration activity log.
     */
    public function logs(Request $request)
    {
        abort_403(user()->permission('view_integration_logs') !== 'all');

        $logs = IntegrationLog::where('company_id', company()->id)
            ->when($request->provider, fn($q) => $q->where('provider', $request->provider))
            ->when($request->status,   fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(50);

        return response()->json($logs);
    }

    private function resolveService(string $provider): ?object
    {
        return match ($provider) {
            'google_calendar' => app(\Modules\TitanIntegrations\Services\Integrations\GoogleCalendarIntegration::class),
            'xero'            => app(\Modules\TitanIntegrations\Services\Integrations\XeroIntegration::class),
            'hubspot'         => app(\Modules\TitanIntegrations\Services\Integrations\HubSpotIntegration::class),
            'mailchimp'       => app(\Modules\TitanIntegrations\Services\Integrations\MailchimpIntegration::class),
            'slack'           => app(\Modules\TitanIntegrations\Services\Integrations\SlackIntegration::class),
            default           => null,
        };
    }
}
