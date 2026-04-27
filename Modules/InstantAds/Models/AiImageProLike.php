<?php

namespace Modules\InstantAds\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiImageProLike extends Model
{
    use HasFactory;

    protected $table = 'ai_image_pro_likes';

    protected $fillable = [
        'ai_image_pro_id',
        'user_id',
        'guest_ip',
    ];

    public function image(): BelongsTo
    {
        return $this->belongsTo(AiImagePro::class, 'ai_image_pro_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
