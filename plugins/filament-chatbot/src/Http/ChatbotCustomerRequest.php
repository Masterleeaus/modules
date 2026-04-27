<?php

namespace App\Extensions\Chatbot\System\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatbotCustomerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'     => ['required', 'string'],
            'email'    => ['required', 'string'],
            'phone'    => ['required', 'string'],
        ];
    }

    protected function prepareForValidation(): void {}
}
