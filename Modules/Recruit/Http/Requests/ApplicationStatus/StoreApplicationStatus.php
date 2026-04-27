<?php

namespace Modules\Recruit\Http\Requests\ApplicationStatus;

use App\Http\Requests\CoreRequest;

class StoreApplicationStatus extends CoreRequest
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

    public function rules()
    {
        return [
            'category_id' => 'required',
            'status' => 'required|unique:recruit_application_status,status',
            'color' => 'required',
        ];
    }

    public function messages()
    {
        $msg = [
            'category_id.required' => __('recruit::messages.categoryRequire'),
        ];

        return $msg;
    }
}
