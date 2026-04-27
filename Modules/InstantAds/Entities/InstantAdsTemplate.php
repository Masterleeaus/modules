<?php

namespace Modules\InstantAds\Entities;

use Illuminate\Database\Eloquent\Model;

class InstantAdsTemplate extends Model
{
    protected $table = 'instant_ads_templates';

    protected $fillable = [
        'key',
        'name',
        'category',
        'job_type',
        'prompt_template',
        'negative_prompt',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByJobType($query, string $jobType)
    {
        return $query->where(function ($q) use ($jobType) {
            $q->whereNull('job_type')->orWhere('job_type', $jobType);
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ── Methods ────────────────────────────────────────────────────────────

    /**
     * Interpolate brand kit values into the prompt template.
     *
     * Supported placeholders: {{brand_name}}, {{tagline}}, {{primary_color}}, {{secondary_color}}
     */
    public function applyBrandKit(InstantAdsBrandKit $kit): string
    {
        return str_replace(
            ['{{brand_name}}', '{{tagline}}', '{{primary_color}}', '{{secondary_color}}'],
            [$kit->name, $kit->tagline ?? '', $kit->primary_color, $kit->secondary_color],
            $this->prompt_template
        );
    }
}
