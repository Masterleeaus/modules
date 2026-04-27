<?php

namespace Modules\Purchase\Http\Requests\VendorCredits;

use App\Http\Requests\CoreRequest;
use App\Traits\CustomFieldsRequestTrait;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVendorCreditRequest extends CoreRequest
{
    use CustomFieldsRequestTrait;
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        $rules = [
            'sub_total' => 'required',
            'total' => 'required',
        ];

        $rules = $this->customFieldRules($rules);

        return $rules;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

    public function authorize()
    {
        return true;
    }

}
