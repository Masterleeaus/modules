<?php

namespace Modules\Purchase\Http\Requests\Inventory;

use App\Http\Requests\CoreRequest;

class StorePurchaseInventoryRequest extends CoreRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'date' => 'required|date',
            'reason_id' => 'required|numeric',
            'type'=> 'required|in:quantity,value',
            'quantity_adjusted*' => 'required_if:type,quantity',
            'adjusted_value*' => 'required_if:type,value'
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'reason_id.required' => __('purchase::messages.inventory.reason'),
            'type.required' => __('purchase::messages.inventory.type')
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

}
