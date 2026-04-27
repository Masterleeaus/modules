<?php
namespace Modules\OnboardingPro\Entities;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $table = 'banners';

    protected $guarded = ['id'];

    protected $casts = [
        'active' => 'boolean',
        'order'  => 'integer',
    ];

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeForRole($query, string $role)
    {
        return $query->where(function ($q) use ($role) {
            $q->where('role', $role)->orWhere('role', 'all');
        });
    }

    // Relations

    public function bannerUsers()
    {
        return $this->hasMany(BannerUser::class);
    }

    // Helpers

    public function isDismissedBy($user): bool
    {
        return $this->bannerUsers()
            ->where('user_id', $user->id)
            ->whereNotNull('dismissed_at')
            ->exists();
    }
}
