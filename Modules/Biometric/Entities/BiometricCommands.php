<?php

namespace Modules\Biometric\Entities;

use App\Models\BaseModel;
use App\Traits\HasCompany;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Modules\Biometric\Entities\BiometricDevice;

class BiometricCommands extends BaseModel
{
    use HasCompany;

    protected $guarded = ['id'];

    protected $table = 'biometric_commands';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'sent_at' => 'datetime',
        'executed_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    public function device()
    {
        return $this->belongsTo(BiometricDevice::class, 'device_serial_number', 'serial_number');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function createUserCommand($commandId, $pin, $name)
    {

        $commandId = "CREATEUSER-{$commandId}";
        return "C:$commandId:DATA USER PIN=$pin\tName=$name\n";
    }

    public static function queryUserCommand($commandId, $pin)
    {
        $commandId = "QUERYUSER-{$commandId}";
        return "C:{$commandId}:DATA QUERY USERINFO PIN={$pin}\n";
    }

    public static function deleteUserCommand($commandId, $pin)
    {
        $commandId = "DELETEUSER-{$commandId}";
        return "C:$commandId:DATA DELETE USERINFO PIN=$pin\n";
    }

    public static function commandExecuted($pendingCommand, $device)
    {
        if (!$pendingCommand) {
            return;
        }

        // Check if this is a user creation command and extract the PIN
        if (strpos($pendingCommand->command_id, 'CREATEUSER') !== false) {

            $employeeId = $pendingCommand->employee_id;

            $biometricEmployee = BiometricEmployee::where('biometric_employee_id', $employeeId)->where('company_id', $device->company_id)->first();

            if (!$biometricEmployee) {
                BiometricEmployee::create([
                    'biometric_employee_id' => $employeeId,
                    'company_id' => $pendingCommand->company_id,
                    'user_id' => $pendingCommand->user_id
                ]);
            }
        }


        $pendingCommand->status = 'executed';
        $pendingCommand->executed_at = now();
        $pendingCommand->save();
    }

    public static function commandFailed($pendingCommand)
    {
        if (!$pendingCommand) {
            return;
        }

        $pendingCommand->status = 'failed';
        $pendingCommand->failed_at = now();
        $pendingCommand->save();
    }

    /**
     * Convert timezone string to minutes offset
     *
     * Examples:
     * - Asia/Kolkata (UTC+5:30) returns 330 (5*60 + 30)
     * - America/New_York (UTC-5:00) returns -300 (-5*60)
     *
     * @param string $timezone Timezone identifier (e.g. 'Asia/Kolkata')
     * @return int Timezone offset in minutes
     */
    public static function timezoneToMinutes($timezone)
    {
        try {
            $dateTimeZone = new \DateTimeZone($timezone);
            $dateTime = new \DateTime('now', $dateTimeZone);
            $offset = $dateTimeZone->getOffset($dateTime) / 60; // Convert seconds to minutes

            return (int) $offset;
        } catch (\Exception $e) {
            Log::error('Error converting timezone to minutes', [
                'timezone' => $timezone,
                'error' => $e->getMessage()
            ]);

            return 330; // Default to UTC if there's an error
        }
    }
}
