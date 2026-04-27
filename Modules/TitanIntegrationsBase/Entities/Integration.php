<?php

namespace Modules\TitanIntegrations\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Integration extends Model
{
    protected $table = 'integrations';

    protected $fillable = [
        'company_id', 'provider', 'credential_type',
        'access_token', 'refresh_token', 'api_key', 'webhook_url',
        'token_expires_at', 'settings', 'status', 'is_byo', 'is_active',
        'last_synced_at', 'error_message',
    ];

    protected $casts = [
        'settings'         => 'array',
        'is_byo'           => 'boolean',
        'is_active'        => 'boolean',
        'token_expires_at' => 'datetime',
        'last_synced_at'   => 'datetime',
    ];

    protected $hidden = ['access_token', 'refresh_token', 'api_key'];

    // -----------------------------------------------------------------------
    // Encrypted credential accessors
    // -----------------------------------------------------------------------

    public function getDecryptedAccessToken(): ?string
    {
        return $this->access_token ? Crypt::decryptString($this->access_token) : null;
    }

    public function getDecryptedRefreshToken(): ?string
    {
        return $this->refresh_token ? Crypt::decryptString($this->refresh_token) : null;
    }

    public function getDecryptedApiKey(): ?string
    {
        return $this->api_key ? Crypt::decryptString($this->api_key) : null;
    }

    public function setAccessTokenAttribute(?string $value): void
    {
        $this->attributes['access_token'] = $value ? Crypt::encryptString($value) : null;
    }

    public function setRefreshTokenAttribute(?string $value): void
    {
        $this->attributes['refresh_token'] = $value ? Crypt::encryptString($value) : null;
    }

    public function setApiKeyAttribute(?string $value): void
    {
        $this->attributes['api_key'] = $value ? Crypt::encryptString($value) : null;
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    public function isConnected(): bool
    {
        return $this->status === 'connected' && $this->is_active;
    }

    public function isTokenExpired(): bool
    {
        return $this->token_expires_at && $this->token_expires_at->isPast();
    }

    public function markConnected(string $accountName = null): void
    {
        $this->status        = 'connected';
        $this->error_message = null;
        if ($accountName) {
            $settings = $this->settings ?? [];
            $settings['account_name'] = $accountName;
            $this->settings = $settings;
        }
        $this->save();
    }

    public function markError(string $message): void
    {
        $this->status        = 'error';
        $this->error_message = $message;
        $this->save();
    }

    public function getConfig(): array
    {
        return config("titanintegrations.integrations.{$this->provider}", []);
    }

    // -----------------------------------------------------------------------
    // Scopes
    // -----------------------------------------------------------------------

    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeConnected($query)
    {
        return $query->where('status', 'connected')->where('is_active', true);
    }
}
