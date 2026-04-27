<?php

namespace Modules\InstantAds\Models;

use App\Models\User;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class AiImagePro extends Model
{
    use HasFactory;
    use HasCompany;

    protected $table = 'ai_image_pro';

    protected $fillable = [
        'user_id',
        'guest_ip',
        'model',
        'engine',
        'prompt',
        'params',
        'status',
        'generated_images',
        'image_width',
        'image_height',
        'metadata',
        'published',
        'likes_count',
        'views_count',
        'share_token',
        'publish_requested_at',
        'publish_reviewed_at',
        'publish_reviewed_by',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'params'               => 'array',
        'generated_images'     => 'array',
        'image_width'          => 'integer',
        'image_height'         => 'integer',
        'metadata'             => 'array',
        'published'            => 'boolean',
        'likes_count'          => 'integer',
        'views_count'          => 'integer',
        'started_at'           => 'datetime',
        'completed_at'         => 'datetime',
        'publish_requested_at' => 'datetime',
        'publish_reviewed_at'  => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'publish_reviewed_by');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(AiImageProLike::class, 'ai_image_pro_id');
    }

    public function isLikedBy(mixed $userIdOrIp = null): bool
    {
        if ($userIdOrIp === null) {
            $userIdOrIp = auth()->check() ? auth()->id() : request()->ip();
        }

        if (is_numeric($userIdOrIp)) {
            return $this->likes()->where('user_id', $userIdOrIp)->exists();
        }

        return $this->likes()->where('guest_ip', $userIdOrIp)->exists();
    }

    public function toggleLike(?int $userId = null, ?string $guestIp = null): bool
    {
        if ($userId) {
            $like = $this->likes()->where('user_id', $userId)->first();
        } else {
            $like = $this->likes()->where('guest_ip', $guestIp)->first();
        }

        if ($like) {
            $like->delete();
            DB::table('ai_image_pro')
                ->where('id', $this->id)
                ->where('likes_count', '>', 0)
                ->decrement('likes_count');
            $this->refresh();
            return false;
        }

        $this->likes()->create([
            'user_id'  => $userId,
            'guest_ip' => $guestIp,
        ]);
        $this->increment('likes_count');

        return true;
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function markAsStarted(): void
    {
        $this->update([
            'status'     => 'processing',
            'started_at' => now(),
        ]);
    }

    public function markAsCompleted(array $images, array $metadata = []): void
    {
        DB::transaction(function () use ($images, $metadata) {
            $record = self::where('id', $this->id)->lockForUpdate()->first();

            $existing      = $record->generated_images ?? [];
            $allImages     = array_merge($existing, $images);
            $expectedCount = $record->params['image_count'] ?? 1;
            $isComplete    = count($allImages) >= $expectedCount;

            $record->update([
                'generated_images' => $allImages,
                'status'           => $isComplete ? 'completed' : 'processing',
                'metadata'         => array_merge($record->metadata ?? [], $metadata),
                'completed_at'     => $isComplete ? now() : null,
            ]);
        });
    }

    public function markAsFailed(string $error): void
    {
        $this->update([
            'status'       => 'failed',
            'metadata'     => array_merge($this->metadata ?? [], ['error' => $error]),
            'completed_at' => now(),
        ]);
    }

    public function requestPublish(): void
    {
        $this->update(['publish_requested_at' => now()]);
    }

    public function hasPendingPublishRequest(): bool
    {
        return ! is_null($this->publish_requested_at) && is_null($this->publish_reviewed_at);
    }

    public function saveDimensions(?string $storagePath = null): void
    {
        if ($this->image_width && $this->image_height) {
            return;
        }

        try {
            $storagePath = $storagePath ?? $this->resolveFirstStoragePath();

            if (! $storagePath) {
                return;
            }

            $extension      = strtolower(pathinfo($storagePath, PATHINFO_EXTENSION));
            $videoExtensions = ['mp4', 'webm', 'mov', 'avi', 'mkv'];

            if (in_array($extension, $videoExtensions, true)) {
                return;
            }

            $fullPath = Storage::disk('public')->path($storagePath);

            if (! file_exists($fullPath)) {
                return;
            }

            $size = getimagesize($fullPath);

            if ($size === false) {
                return;
            }

            $this->update([
                'image_width'  => $size[0],
                'image_height' => $size[1],
            ]);
        } catch (Throwable $e) {
            Log::warning('InstantAds: failed to read image dimensions', [
                'record_id' => $this->id,
                'error'     => $e->getMessage(),
            ]);
        }
    }

    private function resolveFirstStoragePath(): ?string
    {
        $images = $this->generated_images ?? [];

        if (empty($images)) {
            return null;
        }

        $url = $images[0];

        if (str_starts_with($url, '/uploads/')) {
            return substr($url, strlen('/uploads/'));
        }

        return ltrim($url, '/');
    }
}
