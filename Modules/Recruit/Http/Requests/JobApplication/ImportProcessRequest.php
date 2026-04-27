<?php

namespace Modules\Recruit\Http\Requests\JobApplication;

use App\Http\Requests\CoreRequest;

class ImportProcessRequest extends CoreRequest
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
            'recruit_job_id' => 'required',
            'file' => 'required',
            'has_heading' => 'nullable|boolean',
            'columns' => ['required', 'array', 'min:1'],
        ];
    }

    public function attributes()
    {
        return [
            'columns.*' => 'column',
        ];
    }

    public function messages()
    {
        return [
            'recruit_job_id' => __('recruit::messages.selectJobField')
        ];
    }

}
