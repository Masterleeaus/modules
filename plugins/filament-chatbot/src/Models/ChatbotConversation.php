<?php

namespace App\Extensions\Chatbot\System\Models;

use App\Helpers\Classes\MarketplaceHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ChatbotConversation extends Model
{
    protected $table = 'ext_chatbot_conversations';

    protected $fillable = [
        'chatbot_customer_id',
        'chatbot_channel',
        'chatbot_channel_id',
        'customer_channel_id',
        'ip_address',
        'conversation_name',
        'chatbot_id',
        'session_id',
        'connect_agent_at',
        'customer_payload',
        'is_showed_on_history',
        'ticket_status',
        'country_code',
        'pinned',
        'last_activity_at',
        'send_email_at',
        'review_requested_at',
        'review_request_reason',
        'review_submitted_at',
        'review_message',
        'review_selected_response',
    ];

    protected $casts = [
        'chatbot_id'           => 'integer',
        'session_id'           => 'string',
        'customer_payload'     => 'json',
        'is_showed_on_history' => 'boolean',
        'last_activity_at'	    => 'datetime',
        'review_requested_at'  => 'datetime',
        'review_submitted_at'  => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(ChatbotCustomer::class, 'chatbot_customer_id');
    }

    public function chatbotChannel(): BelongsTo
    {
        return $this->belongsTo(ChatbotChannel::class, 'chatbot_channel_id');
    }

    public function sessionId(): string
    {
        return $this->session_id;
    }

    public function chatbot(): BelongsTo
    {
        return $this->belongsTo(Chatbot::class);
    }

    public function lastMessage(): HasOne
    {
        return $this->hasOne(ChatbotHistory::class, 'conversation_id')
            ->where('role', 'user')
            ->orderByDesc('id');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(ChatbotHistory::class, 'conversation_id');
    }

    public function customerTags(): BelongsToMany
    {
        $tagClass = \App\Extensions\ChatbotCustomerTag\System\Models\ChatbotCustomerTag::class;

        if (! MarketplaceHelper::isRegistered('chatbot-customer-tag') || ! class_exists($tagClass)) {
            return $this->belongsToMany(self::class, $this->getTable(), 'id', 'id')
                ->whereRaw('1 = 0');
        }

        return $this->belongsToMany(
            $tagClass,
            'ext_chatbot_conversation_customer_tag',
            'conversation_id',
            'customer_tag_id'
        )->withTimestamps();
    }

    public function widgetHistories(): HasMany
    {
        return $this->hasMany(ChatbotHistory::class, 'conversation_id')->where('is_internal_note', false);
    }
}
