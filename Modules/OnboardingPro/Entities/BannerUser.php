<?php
namespace Modules\OnboardingPro\Entities;

use Illuminate\Database\Eloquent\Model;

class BannerUser extends Model
{
    protected $table = 'banner_user';

    protected $guarded = ['id'];

    protected $casts = [
        'seen_at'      => 'datetime',
        'dismissed_at' => 'datetime',
    ];

    public function banner()
    {
        return $this->belongsTo(Banner::class);
    }
}
