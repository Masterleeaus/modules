<?php

namespace Modules\HRCore\app\Models;

use App\Models\User;
use App\Traits\UserActionsTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\FieldManager\app\Models\Activity;
use Modules\FieldManager\app\Models\DeviceStatusLog;

class AttendanceLog extends Model
{
    use HasFactory, SoftDeletes, UserActionsTrait;

    protected $table = 'attendance_logs';

    protected $fillable = [
        'attendance_id',
        'user_id',
        'date',
        'time',
        'logged_at',
        'type',
        'action_type',
        'shift_id',
        // Location fields
        'latitude',
        'longitude',
        'altitude',
        'speed',
        'speedAccuracy',
        'horizontalAccuracy',
        'verticalAccuracy',
        'course',
        'courseAccuracy',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'distance_from_office',
        // Device and network
        'device_type',
        'device_model',
        'device_id',
        'os_type',
        'os_version',
        'app_version',
        'browser',
        'browser_version',
        'ip_address',
        'network_type',
        'carrier',
        // Validation
        'is_location_verified',
        'is_device_verified',
        'is_suspicious',
        'suspicious_reason',
        // Break information
        'break_type',
        'break_duration',
        // Regularization
        'regularization_id',
        'regularization_reason',
        // Addon verification references
        'geofence_verification_log_id',
        'face_data_id',
        'face_confidence',
        'qr_verification_log_id',
        'dynamic_qr_verification_log_id',
        'ip_verification_log_id',
        'site_id',
        'verification_method',
        // Additional fields
        'notes',
        'status',
        'metadata',
        // Sync tracking
        'is_synced',
        'synced_at',
        'sync_error',
        'retry_count',
        // Audit
        'approved_by_id',
        'approved_at',
        'created_by_id',
        'updated_by_id',
        'tenant_id',
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i:s',
        'logged_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
        'altitude' => 'float',
        'speed' => 'float',
        'speedAccuracy' => 'float',
        'horizontalAccuracy' => 'float',
        'verticalAccuracy' => 'float',
        'course' => 'float',
        'courseAccuracy' => 'float',
        'distance_from_office' => 'float',
        'face_confidence' => 'float',
        'is_location_verified' => 'boolean',
        'is_device_verified' => 'boolean',
        'is_suspicious' => 'boolean',
        'is_synced' => 'boolean',
        'synced_at' => 'datetime',
        'approved_at' => 'datetime',
        'metadata' => 'array',
        'retry_count' => 'integer',
        'break_duration' => 'integer',
    ];

    // Log type constants
    public const TYPE_CHECK_IN = 'check_in';

    public const TYPE_CHECK_OUT = 'check_out';

    public const TYPE_BREAK_START = 'break_start';

    public const TYPE_BREAK_END = 'break_end';

    public const TYPE_LOCATION_UPDATE = 'location_update';

    public const TYPE_REGULARIZATION = 'regularization';

    // Action type constants
    public const ACTION_MANUAL = 'manual';

    public const ACTION_AUTOMATIC = 'automatic';

    public const ACTION_SYSTEM = 'system';

    public const ACTION_API = 'api';

    public const ACTION_MOBILE = 'mobile';

    public const ACTION_WEB = 'web';

    public const ACTION_BIOMETRIC = 'biometric';

    // Verification method constants
    public const VERIFY_WEB = 'web';

    public const VERIFY_MOBILE = 'mobile';

    public const VERIFY_GEOFENCE = 'geofence';

    public const VERIFY_FACE = 'face';

    public const VERIFY_QR = 'qr';

    public const VERIFY_DYNAMIC_QR = 'dynamic_qr';

    public const VERIFY_IP = 'ip';

    public const VERIFY_SITE = 'site';

    public const VERIFY_BIOMETRIC = 'biometric';

    /**
     * Relationships
     */
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function regularization()
    {
        return $this->belongsTo(AttendanceRegularization::class, 'regularization_id');
    }

    // Addon-specific relationships (only loaded when addons are enabled)
    public function geofenceVerificationLog()
    {
        if (class_exists('Modules\GeofenceSystem\app\Models\GeofenceVerificationLog')) {
            return $this->belongsTo('Modules\GeofenceSystem\app\Models\GeofenceVerificationLog', 'geofence_verification_log_id');
        }

        return null;
    }

    public function faceData()
    {
        if (class_exists('Modules\FaceAttendance\app\Models\FaceData')) {
            return $this->belongsTo('Modules\FaceAttendance\app\Models\FaceData', 'face_data_id');
        }

        return null;
    }

    public function qrVerificationLog()
    {
        if (class_exists('Modules\QRAttendance\app\Models\QrCodeVerificationLog')) {
            return $this->belongsTo('Modules\QRAttendance\app\Models\QrCodeVerificationLog', 'qr_verification_log_id');
        }

        return null;
    }

    public function dynamicQrVerificationLog()
    {
        if (class_exists('Modules\DynamicQrAttendance\app\Models\DynamicQrVerificationLog')) {
            return $this->belongsTo('Modules\DynamicQrAttendance\app\Models\DynamicQrVerificationLog', 'dynamic_qr_verification_log_id');
        }

        return null;
    }

    public function ipVerificationLog()
    {
        if (class_exists('Modules\IpAddressAttendance\app\Models\IpAddressVerificationLog')) {
            return $this->belongsTo('Modules\IpAddressAttendance\app\Models\IpAddressVerificationLog', 'ip_verification_log_id');
        }

        return null;
    }

    public function site()
    {
        if (class_exists('Modules\SiteAttendance\app\Models\Site')) {
            return $this->belongsTo('Modules\SiteAttendance\app\Models\Site', 'site_id');
        }

        return null;
    }

    // FieldManager relationships
    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function deviceStatusLogs()
    {
        return $this->hasMany(DeviceStatusLog::class);
    }

    /**
     * Scopes
     */
    public function scopeForUser(Builder $query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForDate(Builder $query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeForDateRange(Builder $query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeByType(Builder $query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeCheckIns(Builder $query)
    {
        return $query->where('type', self::TYPE_CHECK_IN);
    }

    public function scopeCheckOuts(Builder $query)
    {
        return $query->where('type', self::TYPE_CHECK_OUT);
    }

    public function scopeBreaks(Builder $query)
    {
        return $query->whereIn('type', [self::TYPE_BREAK_START, self::TYPE_BREAK_END]);
    }

    public function scopeSuspicious(Builder $query)
    {
        return $query->where('is_suspicious', true);
    }

    public function scopeVerified(Builder $query)
    {
        return $query->where(function ($q) {
            $q->where('is_location_verified', true)
                ->orWhere('is_device_verified', true);
        });
    }

    public function scopeUnsynced(Builder $query)
    {
        return $query->where('is_synced', false);
    }

    public function scopeNeedsRetry(Builder $query)
    {
        return $query->where('is_synced', false)
            ->where('retry_count', '<', 3);
    }

    /**
     * Accessors & Mutators
     */
    public function getIsCheckInAttribute()
    {
        return $this->type === self::TYPE_CHECK_IN;
    }

    public function getIsCheckOutAttribute()
    {
        return $this->type === self::TYPE_CHECK_OUT;
    }

    public function getIsBreakAttribute()
    {
        return in_array($this->type, [self::TYPE_BREAK_START, self::TYPE_BREAK_END]);
    }

    public function getFormattedTimeAttribute()
    {
        return $this->logged_at ? $this->logged_at->format('h:i A') : null;
    }

    public function getFormattedDateTimeAttribute()
    {
        return $this->logged_at ? $this->logged_at->format('d M Y h:i A') : null;
    }

    public function getLocationAttribute()
    {
        if ($this->address) {
            return $this->address;
        }

        $parts = array_filter([
            $this->city,
            $this->state,
            $this->country,
        ]);

        return implode(', ', $parts) ?: null;
    }

    public function getCoordinatesAttribute()
    {
        if ($this->latitude && $this->longitude) {
            return [
                'lat' => $this->latitude,
                'lng' => $this->longitude,
            ];
        }

        return null;
    }

    public function getDeviceInfoAttribute()
    {
        $info = [];

        if ($this->device_type) {
            $info[] = ucfirst($this->device_type);
        }

        if ($this->device_model) {
            $info[] = $this->device_model;
        }

        if ($this->os_type && $this->os_version) {
            $info[] = ucfirst($this->os_type).' '.$this->os_version;
        }

        return implode(' | ', $info) ?: null;
    }

    /**
     * Helper Methods
     */
    public function markAsSuspicious($reason)
    {
        $this->is_suspicious = true;
        $this->suspicious_reason = $reason;
        $this->save();

        return $this;
    }

    public function markAsSynced()
    {
        $this->is_synced = true;
        $this->synced_at = now();
        $this->sync_error = null;
        $this->save();

        return $this;
    }

    public function markAsSyncFailed($error)
    {
        $this->is_synced = false;
        $this->sync_error = $error;
        $this->retry_count++;
        $this->save();

        return $this;
    }

    public function verify($locationType = null)
    {
        if ($locationType === 'location') {
            $this->is_location_verified = true;
        } elseif ($locationType === 'device') {
            $this->is_device_verified = true;
        } else {
            $this->is_location_verified = true;
            $this->is_device_verified = true;
        }

        $this->save();

        return $this;
    }

    public function calculateDistanceFromOffice($officeLat, $officeLng)
    {
        if (! $this->latitude || ! $this->longitude) {
            return null;
        }

        // Haversine formula to calculate distance
        $earthRadius = 6371000; // Earth radius in meters

        $latFrom = deg2rad($officeLat);
        $lonFrom = deg2rad($officeLng);
        $latTo = deg2rad($this->latitude);
        $lonTo = deg2rad($this->longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(
            pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)
        ));

        $distance = $angle * $earthRadius;

        $this->distance_from_office = round($distance, 2);
        $this->save();

        return $this->distance_from_office;
    }

    /**
     * Validation Methods
     */
    public function validateLocation($maxDistance = 500)
    {
        if (! $this->distance_from_office) {
            return true; // No location to validate
        }

        return $this->distance_from_office <= $maxDistance;
    }

    public function validateDevice($allowedDevices = [])
    {
        if (empty($allowedDevices)) {
            return true; // No device restrictions
        }

        return in_array($this->device_id, $allowedDevices);
    }

    public function validateIP($allowedIPs = [])
    {
        if (empty($allowedIPs)) {
            return true; // No IP restrictions
        }

        return in_array($this->ip_address, $allowedIPs);
    }

    /**
     * Static Methods
     */
    public static function createCheckInLog($attendanceId, $userId, array $data = [])
    {
        return self::create(array_merge([
            'attendance_id' => $attendanceId,
            'user_id' => $userId,
            'date' => Carbon::today(),
            'time' => now()->format('H:i:s'),
            'logged_at' => now(),
            'type' => self::TYPE_CHECK_IN,
            'status' => 'active',
        ], $data));
    }

    public static function createCheckOutLog($attendanceId, $userId, array $data = [])
    {
        return self::create(array_merge([
            'attendance_id' => $attendanceId,
            'user_id' => $userId,
            'date' => Carbon::today(),
            'time' => now()->format('H:i:s'),
            'logged_at' => now(),
            'type' => self::TYPE_CHECK_OUT,
            'status' => 'active',
        ], $data));
    }
}
