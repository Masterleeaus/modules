<?php

namespace Modules\Recruit\Http\Requests\OfferLetter;

use App\Http\Requests\CoreRequest;

class StoreOfferLetter extends CoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $data = [
            'jobId' => 'required',
            'jobApplicant' => 'required',
            'jobExpireDate' => 'required',
            'expJoinDate' => 'required',
        ];

        if ($this->add_structure == '1') {
            $data['annual_salary'] = 'required|numeric';
            $data['basic_salary'] = 'required|numeric';
            $data['basic_value'] = 'required';
        } else {
            // $data['comp_amount'] = 'required|numeric';
            // $data['pay_according'] = 'required';

        }

        return $data;
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
            'jobId.required' => 'Job field is required',
            'jobApplicant.required' => 'Job applicant field is required',
            'jobExpireDate.required' => 'Offer expire field is required',
            'expJoinDate.required' => 'Expected joining date field is required',
            'comp_amount.required' => 'Compensation amount field is required',
            'pay_according.required' => 'Pay according field is required',
            'signature.required' => 'Signature is required',

        ];
    }
}
