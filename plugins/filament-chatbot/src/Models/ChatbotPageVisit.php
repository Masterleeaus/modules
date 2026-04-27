<?php

namespace App\Extensions\Chatbot\System\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotPageVisit extends Model
{
    protected $table = 'ext_chatbot_page_visits';

    protected $fillable = [
        'chatbot_id',
        'session_id',
        'page_url',
        'page_title',
        'entered_at',
        'left_at',
    ];

    protected $casts = [
        'chatbot_id'  => 'integer',
        'entered_at'  => 'datetime',
        'left_at'     => 'datetime',
    ];

    public function chatbot(): BelongsTo
    {
        return $this->belongsTo(Chatbot::class);
    }

    public function getDurationAttribute(): ?int
    {
        if (! $this->entered_at) {
            return null;
        }

        $end = $this->left_at ?? now();

        return (int) $this->entered_at->diffInSeconds($end);
    }
}
