<?php

namespace Modules\HRCore\Enums;

enum AttendanceType: string
{
    case OPEN = 'open';

    case QR_CODE = 'qr_code';

    case DYNAMIC_QR = 'dynamic_qr';

    case GEOFENCE = 'geofence';

    case IP_ADDRESS = 'ip_address';

    case SITE = 'site';

    case FACE_RECOGNITION = 'face_recognition';

}
