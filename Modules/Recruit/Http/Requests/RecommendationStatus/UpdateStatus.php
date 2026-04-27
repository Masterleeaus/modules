<?php

namespace Modules\Recruit\Http\Requests\RecommendationStatus;

use App\Http\Requests\CoreRequest;

class UpdateStatus extends CoreRequest
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
            'status' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'status.required' => __('recruit::modules.jobApplication.status').' '.__('app.required'),
        ];
    }
}
