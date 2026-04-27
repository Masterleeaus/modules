<?php

namespace TitanZero\FilamentChatbot\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotEmbedding extends Model
{
    protected $table = 'ext_chatbot_embeddings';

    protected $fillable = [
        'chatbot_id',
        'engine',
        'title',
        'file',
        'url',
        'content',
        'embedding',
        'type',
        'trained_at',
    ];

    protected $casts = [
        'embedding' => 'json',
        'type'      => 'string',
    ];
}
