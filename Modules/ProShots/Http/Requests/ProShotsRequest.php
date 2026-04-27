<?php

namespace Modules\ProShots\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProShotsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image'      => 'required|file|mimes:png,jpg,jpeg|max:5120',
            'background' => 'required|string',
        ];
    }
}
