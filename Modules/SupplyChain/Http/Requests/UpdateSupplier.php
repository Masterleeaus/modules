<?php

namespace Modules\SupplyChain\Http\Requests;
use App\Http\Requests\CoreRequest;

class UpdateSupplier extends CoreRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'                 => 'required',
            'alamat'               => 'required',
            'kode_pos'             => 'required',
            'phone'                => 'required',
            'fax'                  => 'required',
            'contact_person'       => 'required',
            'phone_contact_person' => 'required',
            'email'                => 'required',
        ];
    }

}
