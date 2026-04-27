<?php

namespace Modules\InstantAds\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateAdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'prompt'       => ['required', 'string', 'max:2000'],
            'model'        => ['nullable', 'string'],
            'image_count'  => ['nullable', 'integer', 'min:1', 'max:4'],
            'style'        => ['nullable', 'string'],
            'aspect_ratio' => ['nullable', 'string'],
        ];
    }
}
