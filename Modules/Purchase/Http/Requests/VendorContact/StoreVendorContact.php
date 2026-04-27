<?php

namespace Modules\Purchase\Http\Requests\VendorContact;

use Illuminate\Foundation\Http\FormRequest;

class StoreVendorContact extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'contact_name' => 'required',
            'email' => 'email:rfc|unique:purchase_vendor_contacts,email,null,id,company_id,' . company()->id
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
