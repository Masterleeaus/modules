<?php

namespace Modules\HRCore\App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Nwidart\Modules\Facades\Module;

class ValidAttendanceType implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Open attendance type is always valid
        if ($value === 'open') {
            return true;
        }

        // Map attendance types to their required modules
        $moduleMap = [
            'geofence' => 'GeofenceSystem',
            'ip_address' => 'IpAddressAttendance',
            'qr_code' => 'QRAttendance',
            'site' => 'SiteAttendance',
            'dynamic_qr' => 'DynamicQrAttendance',
            'face_recognition' => 'FaceAttendance',
        ];

        // Check if the attendance type is valid
        if (! isset($moduleMap[$value])) {
            return false;
        }

        // Check if the required module is installed and enabled
        $requiredModule = $moduleMap[$value];

        return Module::has($requiredModule) && Module::isEnabled($requiredModule);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The selected attendance type requires a module that is not installed or enabled.';
    }
}
