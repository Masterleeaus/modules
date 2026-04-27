<?php

namespace Modules\TitanIntegrations\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ApiToken extends Model
{
    protected $table = 'api_tokens';

    protected $fillable = [
        'company_id', 'user_id', 'name', 'token_hash', 'scopes',
        'last_used_at', 'expires_at', 'is_active',
    ];

    protected $casts = [
        'scopes'       => 'array',
        'is_active'    => 'boolean',
        'last_used_at' => 'datetime',
        'expires_at'   => 'datetime',
    ];

    protected $hidden = ['token_hash'];

    public static function generate(int $companyId, int $userId, string $name, array $scopes = [], int $expiryDays = null): array
    {
        $plainToken = 'tk_' . Str::random(48);
        $hash       = hash('sha256', $plainToken);

        $token = static::create([
            'company_id' => $companyId,
            'user_id'    => $userId,
            'name'       => $name,
            'token_hash' => $hash,
            'scopes'     => $scopes,
            'expires_at' => $expiryDays ? now()->addDays($expiryDays) : null,
        ]);

        return ['token' => $plainToken, 'model' => $token];
    }

    public static function findByToken(string $plainToken): ?self
    {
        $hash = hash('sha256', $plainToken);
        return static::where('token_hash', $hash)->where('is_active', true)->first();
    }

    public function hasScope(string $scope): bool
    {
        $scopes = $this->scopes ?? [];
        return in_array('*', $scopes) || in_array($scope, $scopes);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function touchLastUsed(): void
    {
        $this->timestamps = false;
        $this->last_used_at = now();
        $this->save();
        $this->timestamps = true;
    }
}
