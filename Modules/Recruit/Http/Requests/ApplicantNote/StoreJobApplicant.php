<?php

namespace Modules\Recruit\Http\Requests\ApplicantNote;

use App\Http\Requests\CoreRequest;

class StoreJobApplicant extends CoreRequest
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
            'note' => ['required']
        ];
    }

}
