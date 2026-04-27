<?php

namespace Modules\Recruit\Http\Requests;

use App\Http\Requests\CoreRequest;

class StoreWorkExperience extends CoreRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function rules()
    {
        return [
            'work_experience' => 'required',
        ];
    }
}
