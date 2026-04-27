<?php

namespace Modules\Recruit\Http\Requests\Front;

use App\Http\Requests\CoreRequest;

class StoreJobAlertRequest extends CoreRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function rules()
    {

        $data = [
            'email' => 'email|required',
            'job_category' => 'required',
            'location' => 'required',
            'work_experience' => 'required',
            'job_type' => 'required',
        ];

        return $data;
    }

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            //
        ];
    }
}
