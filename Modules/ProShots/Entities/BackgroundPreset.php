<?php

namespace Modules\ProShots\Entities;

use Illuminate\Database\Eloquent\Model;

class BackgroundPreset extends Model
{
    protected $table = 'proshots_background_presets';

    protected $fillable = [
        'name',
        'preset_key',
        'category',
        'description',
        'thumbnail_url',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeForCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
