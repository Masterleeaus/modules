<?php

namespace Modules\Recruit\Http\Requests\OfferLetter;

use App\Http\Requests\CoreRequest;

class StoreJobLetter extends CoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'jobExpireDate' => 'required',
            'expJoinDate' => 'required',
            'comp_amount' => 'required',
            'pay_according' => 'required',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            'jobExpireDate.required' => 'Offer expire field is required',
            'expJoinDate.required' => 'Expected joining date field is required',
            'comp_amount.required' => 'Salary field is required',
            'pay_according.required' => 'Pay according field is required',
        ];
    }
}
