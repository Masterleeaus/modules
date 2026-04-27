<?php

namespace Modules\Purchase\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBill extends FormRequest
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
           'bill_number' => [
                'required',
                Rule::unique('purchase_bills','purchase_bill_number')->where(function ($query) use ($setting) {
                    return $query->where('company_id', $setting->id);
                })],
            'vendor_id' => 'required',
            'purchase_order_id' => 'required|unique:purchase_bills',
            'issue_date' => 'required|date_format:"'. $setting->date_format,
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
