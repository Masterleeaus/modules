<?php

namespace Modules\Biometric\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BiometricDeviceStore extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'device_name' => 'required|string|max:255|unique:biometric_devices,device_name,NULL,id,company_id,' . company()->id,
            'serial_number' => 'required|string|max:100|unique:biometric_devices,serial_number,NULL,id,company_id,' . company()->id
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
