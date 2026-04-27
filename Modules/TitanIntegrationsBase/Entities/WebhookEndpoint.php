<?php

namespace Modules\TitanIntegrations\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WebhookEndpoint extends Model
{
    protected $table = 'webhook_endpoints';

    protected $fillable = [
        'company_id', 'url', 'events', 'secret', 'is_active', 'last_triggered_at',
    ];

    protected $casts = [
        'events'            => 'array',
        'is_active'         => 'boolean',
        'last_triggered_at' => 'datetime',
    ];

    protected $hidden = ['secret'];

    public static function createEndpoint(int $companyId, string $url, array $events): self
    {
        return static::create([
            'company_id' => $companyId,
            'url'        => $url,
            'events'     => $events,
            'secret'     => Str::random(32),
        ]);
    }

    public function listensTo(string $event): bool
    {
        return in_array($event, $this->events ?? []);
    }

    public function sign(string $payload): string
    {
        return hash_hmac('sha256', $payload, $this->secret);
    }
}
