<?php

namespace App\Extensions\Chatbot\System\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatbotCannedResponseRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer'],
            'title'   => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'user_id' => $this->user()->getKey(),
        ]);
    }
}
