<?php

namespace Modules\Accountings\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

/**
 * MVP tenant safety:
 * - If the underlying table has a `user_id` column, automatically scope reads to the
 *   authenticated user and auto-fill `user_id` on create.
 *
 * This is intentionally defensive:
 * - If auth() is unavailable (CLI/migrations) or table is missing, it will no-op.
 * - If the table does not have `user_id`, it will no-op.
 */
trait HasUserScope
{
    protected static function bootHasUserScope(): void
    {
        static::addGlobalScope('accountings_user_id', function (Builder $builder) {
            try {
                if (!auth()->check()) {
                    return;
                }

                $model = $builder->getModel();
                $table = $model->getTable();

                if (Schema::hasColumn($table, 'user_id')) {
                    // Default: strict per-user scoping.
                    // For "template" style rows (tax codes, service lines) a model may opt-in
                    // to also include rows where user_id is NULL.
                    $includeNull = property_exists($model, 'includeNullUserScope')
                        ? (bool) $model->includeNullUserScope
                        : false;

                    if ($includeNull) {
                        $builder->where(function (Builder $q) use ($table) {
                            $q->where("{$table}.user_id", (int) auth()->id())
                              ->orWhereNull("{$table}.user_id");
                        });
                    } else {
                        $builder->where("{$table}.user_id", (int) auth()->id());
                    }
                }
            } catch (\Throwable $e) {
                // no-op (safety)
            }
        });

        static::creating(function ($model) {
            try {
                if (!auth()->check()) {
                    return;
                }

                $table = $model->getTable();
                if (Schema::hasColumn($table, 'user_id') && empty($model->user_id)) {
                    $model->user_id = (int) auth()->id();
                }
            } catch (\Throwable $e) {
                // no-op (safety)
            }
        });
    }
}
