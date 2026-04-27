<?php

namespace Modules\TitanIntegrations\Services\Contracts;

use Modules\TitanIntegrations\Entities\Integration;

interface IntegrationInterface
{
    public function getProvider(): string;
    public function getLabel(): string;
    public function getAuthType(): string;  // 'oauth' | 'api_key' | 'webhook' | 'none'

    /** Test the connection — returns ['ok' => bool, 'account' => string|null, 'error' => string|null] */
    public function testConnection(Integration $integration): array;

    /** Called after OAuth callback — exchange code for tokens */
    public function handleOAuthCallback(string $code, int $companyId): Integration;

    /** Called periodically — perform sync actions (push bookings to calendar, sync clients, etc.) */
    public function sync(Integration $integration): array;

    /** Refresh OAuth token if near expiry */
    public function refreshTokenIfNeeded(Integration $integration): bool;
}
