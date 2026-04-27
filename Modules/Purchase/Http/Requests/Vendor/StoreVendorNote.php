<?php

namespace Modules\Purchase\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class StoreVendorNote extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'title' => 'required',
        ];

        if ($this->type == 1 && is_null($this->user_id)) {
            $rules['user_id'] = 'required';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'user_id.required' => 'The employee field is required.',
        ];
    }

}
