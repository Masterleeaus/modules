<?php

namespace Modules\Recruit\Http\Requests\Evaluation;

use App\Http\Requests\CoreRequest;

class StoreEvaluation extends CoreRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function rules()
    {
        return [
            'status_id' => 'required',
            'details' => 'required',
        ];
    }

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            'status_id.required' => __('recruit::modules.message.selectStatus'),
        ];
    }
}
