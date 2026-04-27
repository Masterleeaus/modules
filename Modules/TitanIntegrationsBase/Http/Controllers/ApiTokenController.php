<?php

namespace Modules\TitanIntegrations\Http\Controllers;

use App\Http\Controllers\AccountBaseController;
use Illuminate\Http\Request;
use Modules\TitanIntegrations\Entities\ApiToken;

class ApiTokenController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'API Tokens';
    }

    public function index()
    {
        abort_403(user()->permission('manage_api_tokens') !== 'all');

        $this->data['tokens'] = ApiToken::where('company_id', company()->id)
            ->where('is_active', true)
            ->latest()
            ->get();

        $this->data['available_scopes'] = [
            'read:bookings', 'write:bookings',
            'read:clients',  'write:clients',
            'read:invoices',
            'read:services',
            'read:providers',
            'read:zones',
            '*',
        ];

        return view('titanintegrations::api-tokens.index', $this->data);
    }

    public function store(Request $request)
    {
        abort_403(user()->permission('manage_api_tokens') !== 'all');

        $request->validate([
            'name'   => 'required|string|max:100',
            'scopes' => 'required|array',
            'expiry_days' => 'nullable|integer|min:1|max:3650',
        ]);

        ['token' => $plainToken, 'model' => $tokenModel] = ApiToken::generate(
            company()->id,
            user()->id,
            $request->input('name'),
            $request->input('scopes', []),
            $request->input('expiry_days'),
        );

        return response()->json([
            'ok'      => true,
            'token'   => $plainToken,   // shown ONCE — not stored in plain
            'token_id' => $tokenModel->id,
            'message' => 'API token created. Copy it now — it will not be shown again.',
        ]);
    }

    public function destroy(int $id)
    {
        abort_403(user()->permission('manage_api_tokens') !== 'all');

        ApiToken::where('company_id', company()->id)->where('id', $id)
            ->update(['is_active' => false]);

        return response()->json(['ok' => true, 'message' => 'Token revoked']);
    }
}
