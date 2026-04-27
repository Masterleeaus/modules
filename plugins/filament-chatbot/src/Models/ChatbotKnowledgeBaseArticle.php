<?php

namespace App\Extensions\Chatbot\System\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotKnowledgeBaseArticle extends Model
{
    protected $table = 'ext_chatbot_knowledge_base_articles';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'content',
        'is_featured',
        'chatbots',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'chatbots'    => 'array',
    ];
}
