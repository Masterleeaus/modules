<?php

namespace Modules\Accountings\Http\Requests;

use App\Http\Requests\CoreRequest;

class StoreType extends CoreRequest
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
            'type_journal_code' => 'required',
            'type_journal'      => 'required',
        ];
    }

}
