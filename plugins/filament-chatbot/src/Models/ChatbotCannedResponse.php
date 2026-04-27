<?php

namespace App\Extensions\Chatbot\System\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotCannedResponse extends Model
{
    protected $table = 'ext_chatbot_canned_responses';

    protected $fillable = [
        'user_id',
        'title',
        'content',
    ];
}
