<?php

namespace App\Models\Concerns;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;

trait TenantScoped
{
    protected static function bootTenantScoped(): void
    {
        static::addGlobalScope(new TenantScope());

        static::creating(function (Model $model) {
            try {
                if (auth()->check() && empty($model->organization_id)) {
                    $model->organization_id = auth()->user()->organization_id;
                }
            } catch (\Throwable) {
                // Guard against CLI/queue contexts where auth is unavailable.
            }
        });
    }
}
