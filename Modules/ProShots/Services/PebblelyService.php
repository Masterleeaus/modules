<?php

namespace Modules\ProShots\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Modules\GlobalSetting\Entities\GlobalSetting;
use Modules\ProShots\Entities\UserPebblely;

class PebblelyService
{
    use PebblelyTrait;

    public const BASE_URL = 'https://api.pebblely.com/';

    public const REMOVE_BG_URL = 'remove-background/v1';

    public const UPSCALE_URL = 'upscale/v1';

    public const CREATE_BACKGROUND_URL = 'create-background/v2';

    public const THEMES_URL = 'themes/v1';

    private ?string $secretKey;

    private Client $client;

    public function __construct()
    {
        $this->secretKey = $this->resolveApiKey();
        $this->client    = new Client();
    }

    private function resolveApiKey(): ?string
    {
        if (!class_exists(GlobalSetting::class)) {
            return null;
        }

        return GlobalSetting::where('key', 'proshots_pebblely_key')->value('value');
    }

    private function get(string $url): array
    {
        if (blank($this->secretKey)) {
            return ['error' => 'API key missing', 'message' => __('Pebblely API key is not configured.')];
        }

        try {
            $response = $this->client->get(self::BASE_URL . $url, [
                'headers' => $this->getHeaders(),
            ])->getBody()->getContents();

            return json_decode($response, true) ?? [];
        } catch (GuzzleException $e) {
            return ['error' => 'API request failed', 'message' => $e->getMessage()];
        }
    }

    private function post(string $url, array $params): array
    {
        if (blank($this->secretKey)) {
            return ['error' => 'API key missing', 'message' => __('Pebblely API key is not configured.')];
        }

        try {
            $response = $this->client->post(self::BASE_URL . $url, [
                'headers' => $this->getHeaders(),
                'json'    => $params,
            ])->getBody()->getContents();

            return json_decode($response, true) ?? [];
        } catch (GuzzleException $e) {
            return ['error' => 'API request failed', 'message' => $e->getMessage()];
        }
    }

    private function getHeaders(): array
    {
        return [
            'X-Pebblely-Access-Token' => $this->secretKey,
            'content-type'            => 'application/json',
        ];
    }

    public function hasApiKey(): bool
    {
        return !blank($this->secretKey);
    }

    public function history(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return UserPebblely::where('user_id', $userId)->latest()->get();
    }
}
