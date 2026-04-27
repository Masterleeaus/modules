<?php

namespace Modules\Recruit\Http\Requests\Recruiter;

use App\Http\Requests\CoreRequest;

class StoreRecruiterRequest extends CoreRequest
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
        return [
            'user_id' => 'required|unique:recruiters',
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => __('recruit::messages.recruiterField'),
        ];
    }
}
