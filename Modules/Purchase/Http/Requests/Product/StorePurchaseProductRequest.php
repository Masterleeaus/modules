<?php

namespace Modules\Purchase\Http\Requests\Product;

use App\Http\Requests\CoreRequest;
use App\Traits\CustomFieldsRequestTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePurchaseProductRequest extends CoreRequest
{
    use CustomFieldsRequestTrait;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $companyId = company()->id;

        $rules = [
            'name' => [
                'required',
                Rule::unique('products')->where(function ($query) use ($companyId) {
                    return $query->where('company_id', $companyId);
                })],   
            'track_inventory' => 'sometimes',
            'type' => 'required|in:goods,service',
            'selling_price' => 'required|numeric',
            'purchase_information' => 'sometimes',
            'opening_stock' => 'required_if:track_inventory,1',
            'purchase_price' => 'required_if:purchase_information,1,numeric',
            'downloadable_file' => 'required_if:downloadable,true|file',
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
