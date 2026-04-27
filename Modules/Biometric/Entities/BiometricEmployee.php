<?php

namespace Modules\Biometric\Entities;

use App\Models\BaseModel;
use App\Models\User;
use App\Traits\HasCompany;
use Illuminate\Support\Facades\Log;

use App\Models\EmployeeDetails;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class BiometricEmployee extends BaseModel
{
    use HasCompany;

    protected $guarded = ['id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function recordFingerprint($rows, $device)
    {
        foreach ($rows as $row) {
            if (empty($row)) continue;

            $parts = explode("\t", $row);
            Log::info('Parts: ' . json_encode($parts));

            if (count($parts) > 0) {
                self::processRow($parts, $device);
            }
        }
    }

    private static function processRow($parts, $device)
    {
        if (strpos($parts[0], 'FP PIN=') === 0) {
            self::handleFingerprintData($parts, $device);
        } elseif (strpos($parts[0], 'USER PIN=') === 0) {
            self::handleUserData($parts, $device);
        } elseif (strpos($parts[0], 'BIOPHOTO PIN=') === 0) {
            self::handlePhotoData($parts, $device);
        }
    }

    private static function handleFingerprintData($parts, $device)
    {
        $employeeId = str_replace('FP PIN=', '', $parts[0]);
        $fingerprintId = null;
        $template = null;

        foreach ($parts as $part) {
            if (strpos($part, 'FID=') === 0) {
                $fingerprintId = str_replace('FID=', '', $part);
            } elseif (strpos($part, 'TMP=') === 0) {
                $template = str_replace('TMP=', '', $part);
            }
        }

        Log::info('Fingerprint data received', [
            'device_serial_number' => $device->serial_number,
            'employee_id' => $employeeId,
            'has_fingerprint' => true,
            'fingerprint_id' => $fingerprintId,
            'fingerprint_template' => $template
        ]);

        if ($employeeId && $fingerprintId) {
            self::updateOrCreateBiometricEmployee(
                $employeeId,
                $device->company_id,
                [
                    'has_fingerprint' => true,
                    'fingerprint_id' => $fingerprintId,
                    'fingerprint_template' => $template
                ]
            );
        }
    }

    private static function handleUserData($parts, $device)
    {
        $employeeId = str_replace('USER PIN=', '', $parts[0]);
        $cardNumber = null;

        foreach ($parts as $part) {
            if (strpos($part, 'Card=') === 0) {
                $cardNumber = str_replace('Card=', '', $part);
            }
        }

        Log::info('User data received', [
            'employee_id' => $employeeId,
            'card_number' => $cardNumber
        ]);

        if ($employeeId && $cardNumber) {
            self::updateOrCreateBiometricEmployee(
                $employeeId,
                $device->company_id,
                ['card_number' => $cardNumber]
            );
        }
    }

    private static function handlePhotoData($parts, $device)
    {
        $employeeId = str_replace('BIOPHOTO PIN=', '', $parts[0]);
        $photo = null;

        foreach ($parts as $part) {
            if (strpos($part, 'Content=') === 0) {
                $photo = str_replace('Content=', '', $part);
            }
        }

        Log::info('Photo data received', [
            'employee_id' => $employeeId,
            'photo' => $photo
        ]);

        if ($employeeId && $photo) {
            self::updateOrCreateBiometricEmployee(
                $employeeId,
                $device->company_id,
                [
                    'has_photo' => true,
                    'photo' => $photo
                ]
            );
        }
    }

    private static function updateOrCreateBiometricEmployee($employeeId, $companyId, $data)
    {
        return self::updateOrCreate(
            [
                'biometric_employee_id' => $employeeId,
                'company_id' => $companyId
            ],
            $data
        );
    }

    public static function markAttendanceToDeviceAndApplication($rows, $device, $request)
    {
        foreach ($rows as $line) {

            $parts = explode("\t", $line);

            Log::info('Parts: ' . json_encode($parts));

            if (count($parts) >= 2) {
                $deviceEmployeeId = $parts[0];
                $timestamp = $parts[1];
                $status = $parts[2] ?? 0;


                // Skip if timestamp is 0 or not in a valid date time format
                if ($timestamp == 0 || !strtotime($timestamp)) {
                    continue;
                }

                $timestamp = Carbon::parse((string)$timestamp, $device->company->timezone)
                    ->setTimezone('UTC')
                    ->format('Y-m-d H:i:s');

                Log::info('Timestamp: ' . $timestamp);
                Log::info('Status: ' . $status);


                // Check if the record already exists
                $existingRecord = \DB::table('biometric_device_attendances')
                    ->where('employee_id', $deviceEmployeeId)
                    ->where('timestamp', $timestamp)
                    ->where('device_serial_number', $device->serial_number)
                    ->where('company_id', $device->company_id)
                    ->first();

                if ($existingRecord) {
                    continue;
                }


                $biometricEmployee = BiometricEmployee::where('biometric_employee_id', $deviceEmployeeId)->where('company_id', $device->company_id)->first();

                // If this is a clock in (status=0) and there's already a clock in for this employee today,
                // set status to 1 (clock out)

                // Get the last attendance record for this employee on this day
                $timestampDate = date('Y-m-d', strtotime($timestamp));

                $lastRecord = \DB::table('biometric_device_attendances')
                    ->where('employee_id', $deviceEmployeeId)
                    ->whereDate('timestamp', $timestampDate)
                    ->orderBy('timestamp', 'desc')
                    ->where('company_id', $device->company_id)
                    ->first();

                // Default to clock in (0) if no record exists
                $status = 0;

                // If last record exists and is a clock in (0), then this should be a clock out (1)
                if ($lastRecord && $lastRecord->status1 == 0) {
                    $status = 1; // Clock out
                }
                // If last record exists and is a clock out (1), then this should be a clock in (0)
                else if ($lastRecord && $lastRecord->status1 == 1) {
                    $status = 0; // Clock in
                }


                $attendances = [
                    'device_name' => $device->device_name,
                    'device_serial_number' => $device->serial_number,
                    'user_id' => $biometricEmployee ? $biometricEmployee->user_id : null,
                    'company_id' => $device->company_id,
                    'table' => $request->input('table') ?? ' ',
                    'stamp' => $request->input('Stamp') ?? ' ',
                    'employee_id' => $deviceEmployeeId,
                    'timestamp' => $timestamp,
                    'status1' => $status,
                    'status2' => self::validateAndFormatInteger($parts[3]) ?? -1,
                    'status3' => self::validateAndFormatInteger($parts[4]) ?? -1,
                    'status4' => self::validateAndFormatInteger($parts[5]) ?? -1,
                    'status5' => self::validateAndFormatInteger($parts[6]) ?? -1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];


                \DB::table('biometric_device_attendances')->insert($attendances);

                if (!$biometricEmployee) {
                    $employeeDetails = EmployeeDetails::where('employee_id', $deviceEmployeeId)->where('company_id', $device->company_id)->first();

                    if ($employeeDetails) {
                        $biometricEmployee = BiometricEmployee::create([
                            'biometric_employee_id' => $deviceEmployeeId,
                            'company_id' => $device->company_id,
                            'user_id' => $employeeDetails->user_id
                        ]);
                    }
                }

                self::markAttendance($biometricEmployee->user, $timestamp);
            }
        }
    }


    private static function markAttendance($user, $timestamp)
    {
        $clockIn = Carbon::createFromFormat('Y-m-d H:i:s', $timestamp, $user->company->timezone);
        $appTimezone = $clockIn->copy();
        $carbonDate = $clockIn->copy()->startOfDay();

        // Get the last attendance record for this user on this day
        $lastAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('clock_in_time', $carbonDate)
            ->orderBy('clock_in_time', 'desc')
            ->first();

        // If no record exists or last record has clock_out_time, create a new clock in
        if (!$lastAttendance || $lastAttendance->clock_out_time !== null) {
            // Clock In
            $user->attendance()->create([
                'clock_in_time' => $appTimezone,
                'half_day' => 'no',
                'clock_in_type' => 'biometric',
                'work_from_type' => 'office',
                'clock_in_ip' => request()->ip()
            ]);
        } else {
            // Clock Out - if last record exists and has no clock_out_time
            $lastAttendance->update([
                'clock_out_time' => $appTimezone,
                'clock_out_type' => 'biometric',
                'work_from_type' => 'office',
                'clock_out_ip' => request()->ip()
            ]);
        }
    }

    private static function validateAndFormatInteger($value)
    {
        // Check if value is set and not empty string
        if (isset($value) && $value !== '') {
            // Ensure it's a valid numeric value before casting
            return is_numeric($value) ? (int) $value : null;
        }

        return null;
    }
}
