<?php

namespace Modules\Recruit\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobType extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'job_type' => 'required',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
