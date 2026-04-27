<?php

namespace App\Extensions\Chatbot\System\Http\Requests;

use App\Domains\Entity\Enums\EntityEnum;
use App\Extensions\Chatbot\System\Models\ChatbotAvatar;
use App\Helpers\Classes\Helper;
use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChatbotStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'uuid'                          => ['required', 'string'],
            'user_id'                       => ['required', 'integer', 'exists:users,id'],
            'title'                         => ['required', 'string'],
            'bubble_message'                => ['required', 'string'],
            'welcome_message'               => ['required', 'string'],
            'interaction_type'              => ['required', 'string'],
            'instructions'                  => Helper::appIsNotDemo() ? ['required', 'string'] : ['sometimes', 'nullable', 'string'],
            'do_not_go_beyond_instructions' => ['required', 'boolean'],
            'suggested_prompts'             => ['sometimes', 'nullable', 'array'],
            'suggested_prompts.*.name'      => ['sometimes', 'nullable', 'string'],
            'suggested_prompts.*.prompt'    => ['sometimes', 'nullable', 'string'],
            'suggested_prompts_enabled'     => ['sometimes', 'boolean'],
            'language'                      => ['sometimes', 'nullable', 'string'],
            'ai_model'                      => ['required', 'string'],
            'ai_embedding_model'            => ['required', 'string'],
            'avatar'                        => ['nullable', 'sometimes'],
            'human_agent_conditions'        => ['sometimes', 'nullable', 'array'],
            'is_booking_assistant'          => ['sometimes', 'boolean'],
            'booking_assistant_conditions'  => ['sometimes', 'nullable', 'array'],
            'booking_assistant_iframe'      => ['sometimes', 'nullable', 'string'],
            'voice_call_enabled'            => ['sometimes', 'boolean'],
            'voice_call_first_message'      => ['nullable', 'string'],
            'trusted_domains'               => ['sometimes', 'nullable', 'array'],
            'is_review_enabled'             => ['sometimes', 'nullable', 'boolean'],
            'review_prompt'                 => ['sometimes', 'nullable', 'string'],
            'review_responses'              => ['sometimes', 'nullable', 'array', 'max:5'],
            'review_responses.*'            => ['nullable', 'string'],
            'is_shop'                       => ['sometimes', 'boolean'],
            'shop_source'					              => ['sometimes', 'nullable', 'string'],
            'shop_features'                 => ['sometimes', 'nullable', 'array'],
            'shopify_domain'				            => ['sometimes', 'nullable', 'string'],
            'shopify_access_token'			       => ['sometimes', 'nullable', 'string'],
            'woocommerce_domain'			         => ['sometimes', 'nullable', 'string'],
            'woocommerce_consumer_key'      => ['sometimes', 'nullable', 'string'],
            'woocommerce_consumer_secret'   => ['sometimes', 'nullable', 'string'],
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

        $domains = is_array($this->trusted_domains)
            ? $this->trusted_domains
            : explode(',', trim($this->trusted_domains ?? ''));

        $trusted_domains = array_values(
            array_filter(
                array_map(
                    fn ($domain) => rtrim(
                        preg_replace('#^(https?://)?#i', '', trim($domain)),
                        '/'
                    ),
                    $domains
                )
            )
        );

        $this->merge([
            'avatar'                    => $this->input('avatar') ?: ChatbotAvatar::query()->first()?->getAttribute('avatar'),
            'uuid'                      => Str::uuid()->toString(),
            'user_id'                   => Auth::id(),
            'ai_model'                  => Setting::getCache()->openai_default_model,
            'ai_embedding_model'        => $this->get('ai_embedding_model') ?: EntityEnum::TEXT_EMBEDDING_3_SMALL->value,
            'suggested_prompts'         => $suggestedPrompts,
            'suggested_prompts_enabled' => (bool) $this->boolean('suggested_prompts_enabled'),
            'trusted_domains'           => $trusted_domains,
        ]);
    }
}
