<?php

namespace Modules\TitanIntegrations\Entities;

use Illuminate\Database\Eloquent\Model;

class IntegrationLog extends Model
{
    protected $table = 'integration_logs';

    public $timestamps = false;

    protected $fillable = [
        'company_id', 'provider', 'direction', 'event_type',
        'payload', 'status', 'http_status', 'error_message',
        'attempts', 'processed_at',
    ];

    protected $casts = [
        'payload'      => 'array',
        'processed_at' => 'datetime',
        'created_at'   => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            $model->created_at = now();
        });
    }
}
