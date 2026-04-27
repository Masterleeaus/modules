<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        try {
            if (! auth()->check()) {
                return;
            }

            $user = auth()->user();

            if ($user->hasRole('super_admin')) {
                return;
            }

            if (! $user->organization_id) {
                return;
            }

            $builder->where(
                $model->getTable().'.organization_id',
                $user->organization_id
            );
        } catch (\Throwable) {
            // Guard against CLI/queue contexts where auth or schema is unavailable.
        }
    }
}
