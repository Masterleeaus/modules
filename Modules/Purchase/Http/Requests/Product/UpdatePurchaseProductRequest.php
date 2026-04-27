<?php

namespace Modules\Purchase\Http\Requests\Product;

use App\Http\Requests\CoreRequest;
use App\Traits\CustomFieldsRequestTrait;

class UpdatePurchaseProductRequest extends CoreRequest
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
            'name' => 'required|unique:products,name,'.$this->route('purchase_product').',id,company_id,' . company()->id,
            'track_inventory' => 'sometimes',
            'type' => 'required|in:goods,service',
            'selling_price' => 'required|numeric',
            'purchase_information' => 'sometimes',
            'downloadable_file' => 'nullable|file',
            'opening_stock' => 'required_if:track_inventory,1',
            'purchase_price' => 'required_if:purchase_information,1,numeric',

        ];

        $rules = $this->customFieldRules($rules);

        return $rules;
    }

    public function messages()
    {
        return [
            'opening_stock.required_if' => __('purchase::messages.openingStockRequired'),
            'rate_per_unit.required_if' => __('purchase::messages.ratePerUnitRequired'),
            'selling_price.required_if' => __('purchase::messages.sellingPriceRequired'),
            'purchase_price.required_if' => __('purchase::messages.purchasePriceRequired'),
            'downloadable_file.required_if' => __('validation.required', ['attribute' => __('app.downloadableFile')]),
        ];
    }

    public function attributes()
    {
        $attributes = [];

        $attributes = $this->customFieldsAttributes($attributes);

        return $attributes;
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
