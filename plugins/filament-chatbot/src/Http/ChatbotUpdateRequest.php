<?php

namespace App\Extensions\Chatbot\System\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChatbotUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title'                         => ['required', 'string'],
            'bubble_message'                => ['required', 'string'],
            'welcome_message'               => ['required', 'string'],
            'instructions'                  => ['required', 'string'],
            'do_not_go_beyond_instructions' => ['required', 'boolean'],
            'suggested_prompts'             => ['sometimes', 'nullable', 'array'],
            'suggested_prompts.*.name'      => ['sometimes', 'nullable', 'string'],
            'suggested_prompts.*.prompt'    => ['sometimes', 'nullable', 'string'],
            'suggested_prompts_enabled'     => ['sometimes', 'boolean'],
            'language'                      => ['sometimes', 'nullable', 'string'],
            'ai_model'                      => ['required', 'string'],
            'is_booking_assistant'          => ['sometimes', 'boolean'],
            'booking_assistant_iframe'      => ['sometimes', 'nullable', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $suggestedPrompts = collect($this->input('suggested_prompts', []))
            ->map(fn ($prompt) => [
                'name'   => Arr::get($prompt, 'name'),
                'prompt' => Arr::get($prompt, 'prompt'),
            ])
            ->filter(fn ($prompt) => filled($prompt['name']) || filled($prompt['prompt']))
            ->values()
            ->all();

        $this->merge([
            'uuid'                          => Str::uuid()->toString(),
            'user_id'                       => Auth::id(),
            'suggested_prompts'             => $suggestedPrompts,
            'suggested_prompts_enabled'     => (bool) $this->boolean('suggested_prompts_enabled'),
        ]);
    }
}
