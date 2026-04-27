<?php

namespace App\Extensions\Chatbot\System\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatbotHistoryStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'prompt' => 'required_without:media|nullable|string',
            'role'   => 'sometimes|string',
            'media'  => 'sometimes|file|mimes:' . setting('media_allowed_types', 'jpg,png,gif,webp,svg,mp4,avi,mov,wmv,flv,webm,mp3,wav,m4a,pdf,doc,docx,xls,xlsx') . '|max:20480',
        ];
    }
}
