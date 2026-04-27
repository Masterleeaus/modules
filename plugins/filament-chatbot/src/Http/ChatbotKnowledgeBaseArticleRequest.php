<?php

namespace App\Extensions\Chatbot\System\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatbotKnowledgeBaseArticleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'user_id'    => ['required', 'integer'],
            'title'      => ['required', 'string'],
            'description'=> ['required', 'string'],
            'content'    => ['sometimes', 'nullable', 'string'],
            'is_featured'=> ['boolean'],
            'chatbots'   => ['sometimes', 'nullable', 'array'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'user_id'     => $this->user()->getKey(),
            'is_featured' => $this->has('is_featured'),
        ]);
    }
}
