<?php

namespace Modules\TitanIntegrations\Http\Controllers;

use App\Http\Controllers\AccountBaseController;
use Illuminate\Http\Request;
use Modules\TitanIntegrations\Entities\IntegrationLog;
use Modules\TitanIntegrations\Entities\WebhookEndpoint;
use Modules\TitanIntegrations\Services\WebhookDispatcher;

class WebhookEndpointController extends AccountBaseController
{
    public function __construct(private WebhookDispatcher $dispatcher)
    {
        parent::__construct();
        $this->pageTitle = 'Webhooks';
    }

    public function index()
    {
        abort_403(user()->permission('manage_webhooks') !== 'all');

        $this->data['endpoints']       = WebhookEndpoint::where('company_id', company()->id)->get();
        $this->data['available_events'] = config('titanintegrations.webhooks.events', []);

        return view('titanintegrations::webhooks.index', $this->data);
    }

    public function store(Request $request)
    {
        abort_403(user()->permission('manage_webhooks') !== 'all');

        $request->validate([
            'url'    => 'required|url|max:500',
            'events' => 'required|array|min:1',
        ]);

        $endpoint = WebhookEndpoint::createEndpoint(
            company()->id,
            $request->input('url'),
            $request->input('events'),
        );

        return response()->json([
            'ok'       => true,
            'id'       => $endpoint->id,
            'secret'   => $endpoint->secret,
            'message'  => 'Webhook endpoint registered. Store your secret — it will not be shown again.',
        ]);
    }

    public function destroy(int $id)
    {
        abort_403(user()->permission('manage_webhooks') !== 'all');

        WebhookEndpoint::where('company_id', company()->id)->where('id', $id)->delete();
        return response()->json(['ok' => true]);
    }

    /**
     * Send a test event to an endpoint.
     */
    public function test(int $id)
    {
        abort_403(user()->permission('manage_webhooks') !== 'all');

        $endpoint = WebhookEndpoint::where('company_id', company()->id)->findOrFail($id);
        $this->dispatcher->dispatch($endpoint, 'ping', ['message' => 'WorkSuite webhook test delivery']);

        return response()->json(['ok' => true, 'message' => 'Test event sent']);
    }

    /**
     * Delivery log for a specific endpoint.
     */
    public function logs(int $id)
    {
        abort_403(user()->permission('view_integration_logs') !== 'all');

        WebhookEndpoint::where('company_id', company()->id)->findOrFail($id);

        $logs = IntegrationLog::where('company_id', company()->id)
            ->where('provider', 'webhook')
            ->latest()
            ->paginate(50);

        return response()->json($logs);
    }
}
