<?php

namespace Modules\TitanIntegrations\Services;

use Illuminate\Support\Facades\Http;
use Modules\TitanIntegrations\Entities\Integration;

class OAuthService
{
    /**
     * Build the OAuth authorization URL for a provider.
     */
    public function buildAuthUrl(string $provider, int $companyId): string
    {
        $cfg = config("titanintegrations.integrations.{$provider}");

        $state = base64_encode(json_encode(['provider' => $provider, 'company_id' => $companyId]));
        session(["oauth_state_{$provider}" => $state]);

        $params = http_build_query([
            'client_id'     => $cfg['client_id'],
            'redirect_uri'  => $cfg['redirect_uri'],
            'scope'         => implode(' ', $cfg['scopes'] ?? []),
            'response_type' => 'code',
            'access_type'   => 'offline',
            'state'         => $state,
        ]);

        return $this->getAuthEndpoint($provider) . '?' . $params;
    }

    /**
     * Exchange auth code for access + refresh tokens and persist on the Integration record.
     */
    public function exchangeCode(string $provider, string $code, Integration $integration): void
    {
        $cfg = config("titanintegrations.integrations.{$provider}");

        $response = Http::post($this->getTokenEndpoint($provider), [
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'client_id'     => $cfg['client_id'],
            'client_secret' => $cfg['client_secret'],
            'redirect_uri'  => $cfg['redirect_uri'],
        ]);

        $data = $response->json();

        $integration->access_token    = $data['access_token'] ?? null;
        $integration->refresh_token   = $data['refresh_token'] ?? $integration->getDecryptedRefreshToken();
        $integration->token_expires_at = isset($data['expires_in'])
            ? now()->addSeconds((int) $data['expires_in'])
            : null;
        $integration->credential_type = 'oauth';
        $integration->save();
    }

    /**
     * Refresh an expiring access token using the stored refresh token.
     */
    public function refreshToken(Integration $integration): bool
    {
        $provider     = $integration->provider;
        $cfg          = config("titanintegrations.integrations.{$provider}");
        $refreshToken = $integration->getDecryptedRefreshToken();

        if (!$refreshToken) return false;

        $response = Http::post($this->getTokenEndpoint($provider), [
            'grant_type'    => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id'     => $cfg['client_id'],
            'client_secret' => $cfg['client_secret'],
        ]);

        if (!$response->successful()) {
            $integration->markError('Token refresh failed: ' . $response->body());
            return false;
        }

        $data = $response->json();
        $integration->access_token    = $data['access_token'];
        $integration->token_expires_at = isset($data['expires_in'])
            ? now()->addSeconds((int) $data['expires_in'])
            : null;
        $integration->status = 'connected';
        $integration->save();

        return true;
    }

    private function getAuthEndpoint(string $provider): string
    {
        return match ($provider) {
            'google_calendar', 'google_sheets', 'google_business'
                                => 'https://accounts.google.com/o/oauth2/v2/auth',
            'xero'              => 'https://login.xero.com/identity/connect/authorize',
            'quickbooks'        => 'https://appcenter.intuit.com/connect/oauth2',
            'myob'              => 'https://secure.myob.com/oauth2/account/authorize',
            'outlook_calendar'  => 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize',
            'salesforce'        => 'https://login.salesforce.com/services/oauth2/authorize',
            'servicem8'         => 'https://go.servicem8.com/oauth/authorize',
            default             => throw new \InvalidArgumentException("No OAuth endpoint for {$provider}"),
        };
    }

    private function getTokenEndpoint(string $provider): string
    {
        return match ($provider) {
            'google_calendar', 'google_sheets', 'google_business'
                                => 'https://oauth2.googleapis.com/token',
            'xero'              => 'https://identity.xero.com/connect/token',
            'quickbooks'        => 'https://oauth.platform.intuit.com/oauth2/v1/tokens/bearer',
            'myob'              => 'https://secure.myob.com/oauth2/v1/authorize',
            'outlook_calendar'  => 'https://login.microsoftonline.com/common/oauth2/v2.0/token',
            'salesforce'        => 'https://login.salesforce.com/services/oauth2/token',
            'servicem8'         => 'https://go.servicem8.com/oauth/access_token',
            default             => throw new \InvalidArgumentException("No token endpoint for {$provider}"),
        };
    }
}
