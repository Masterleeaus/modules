<?php

namespace Modules\TitanIntegrations\Services\Integrations;

use Illuminate\Support\Facades\Http;
use Modules\TitanIntegrations\Entities\Integration;

/**
 * WordPress REST API — publish content using Application Passwords.
 */
class WordPressIntegration
{
    public function getProvider(): string { return 'wordpress'; }

    public function testConnection(Integration $integration): array
    {
        $siteUrl  = $integration->settings['site_url'] ?? null;
        $username = $integration->settings['username'] ?? null;
        $appPass  = $integration->getDecryptedApiKey();

        if (!$siteUrl || !$username || !$appPass) {
            return ['ok' => false, 'error' => 'Site URL, username and application password are required'];
        }

        $response = Http::withBasicAuth($username, $appPass)
            ->get(rtrim($siteUrl, '/') . '/wp-json/wp/v2/users/me');

        return $response->successful()
            ? ['ok' => true, 'account' => $response->json('name', $username) . ' @ ' . parse_url($siteUrl, PHP_URL_HOST)]
            : ['ok' => false, 'error' => $response->json('message', 'Authentication failed')];
    }

    /**
     * Create a new WordPress post.
     */
    public function createPost(Integration $integration, array $post): ?int
    {
        [$baseUrl, $auth] = $this->getClientConfig($integration);
        if (!$baseUrl) return null;

        $response = Http::withBasicAuth(...$auth)
            ->post("{$baseUrl}/wp-json/wp/v2/posts", [
                'title'   => $post['title'],
                'content' => $post['content'],
                'status'  => $post['status'] ?? 'draft',
                'slug'    => $post['slug'] ?? null,
                'excerpt' => $post['excerpt'] ?? null,
            ]);

        return $response->successful() ? $response->json('id') : null;
    }

    /**
     * Upload media (image) to WordPress media library.
     */
    public function uploadMedia(Integration $integration, string $filePath, string $filename): ?int
    {
        [$baseUrl, $auth] = $this->getClientConfig($integration);
        if (!$baseUrl) return null;

        $response = Http::withBasicAuth(...$auth)
            ->attach('file', file_get_contents($filePath), $filename)
            ->post("{$baseUrl}/wp-json/wp/v2/media");

        return $response->successful() ? $response->json('id') : null;
    }

    private function getClientConfig(Integration $integration): array
    {
        $siteUrl  = $integration->settings['site_url'] ?? null;
        $username = $integration->settings['username'] ?? null;
        $appPass  = $integration->getDecryptedApiKey();

        if (!$siteUrl || !$username || !$appPass) return [null, []];

        return [rtrim($siteUrl, '/'), [$username, $appPass]];
    }
}
