<?php

namespace Modules\Recruit\Http\Requests;

use App\Http\Requests\CoreRequest;

class StoreJobSubCategory extends CoreRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function rules()
    {
        return [
            'sub_category_name' => 'required',
        ];
    }
}
