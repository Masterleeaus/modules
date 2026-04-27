<?php

namespace Modules\Purchase\Http\Requests\PurchaseOrder;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $setting = company();

        $rules = [
            'purchase_order_number' => [
                'required',
                Rule::unique('purchase_orders')->where('company_id', company()->id)
            ],
            'vendor_id' => 'required',
            'purchase_date' => 'required|date_format:"' . $setting->date_format . '"|before_or_equal:expected_date',
            'expected_date' => 'required|date_format:"' . $setting->date_format . '"|after_or_equal:purchase_date',
            'exchange_rate' => 'required',
        ];
        return $rules;
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

}
