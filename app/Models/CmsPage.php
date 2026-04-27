<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CmsPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'summary',
        'meta_title',
        'meta_description',
        'status',
        'website_content',
        'published_at',
    ];

    protected $casts = [
        'website_content' => 'array',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (CmsPage $page): void {
            if (blank($page->slug) && filled($page->title)) {
                $page->slug = Str::slug($page->title);
            }
        });
    }

    public function isPublished(): bool
    {
        return $this->status === 'published'
            && (! $this->published_at || $this->published_at->isPast());
    }
}
