<?php

namespace Modules\Biometric\Http\Requests;

use App\Http\Requests\CoreRequest;

class UpdateRequest extends CoreRequest
{

    /**
     * @return bool
     *
     * @throws \Froiden\RestAPI\Exceptions\UnauthorizedException
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
            'username' => 'required',
            'password' => 'required',
            'api_url' => 'required',
        ];
    }

    public function messages()
    {
        return [
            //
        ];
    }
}
