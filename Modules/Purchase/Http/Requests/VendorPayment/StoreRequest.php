<?php

namespace Modules\Purchase\Http\Requests\VendorPayment;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        $data = [];

        $data = [
            // 'vendor_id' => 'required',
            'payment_made' => 'required|gt:0',
            'payment_date' => 'required',
            'excess' => 'required|numeric|min:0'
        ];

        request()->due = is_null(request()->due) ? [] : request()->due;

        if(in_array(1, request()->due)){
            $data['due'] = 'required|numeric|min:0';
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
            'excess.require' => __('purchase::modules.vendorPayment.shouldBeGreater')
        ];
    }

}
