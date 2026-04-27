<?php

namespace Modules\Biometric\Http\Controllers;



use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\Controller;
use Modules\Biometric\Entities\BiometricDevice;
use Modules\Biometric\Entities\BiometricCommands;

use Modules\Biometric\Entities\BiometricEmployee;

class ZKTecoController extends Controller
{

    /**
     * Handle incoming attendance data from ZKTeco devices
     */
    public function handleAttendanceData(Request $request)
    {

        $sn = strtoupper($request->input('SN'));

        if (!$sn) {
            return response("ERROR: Missing device SN", 400)->header('Content-Type', 'text/plain');
        }

        $device = BiometricDevice::where('serial_number', $sn)->first();

        if (!$device) {
            return response("Device not found", 404)->header('Content-Type', 'text/plain');
        }

        // update status device
        $device->update([
            'status' => 'online',
            'device_ip' => request()->ip(),
            'last_online' => now()
        ]);

        $rawContent = $request->getContent();
        // Split raw input by newlines in case of multiple logs
        $rows = preg_split('/\r\n|\r|\n/', $rawContent);

        Log::info('Rows: ' . json_encode($rows));

        // If this contains fingerprint data (FP PIN=)
        // Check if the content contains any biometric data (fingerprint, user, card, or photo)
        $hasBiometricData = (
            strpos($rawContent, 'FP PIN=') !== false ||
            strpos($rawContent, 'USER PIN=') !== false ||
            strpos($rawContent, 'Card=') !== false ||
            strpos($rawContent, 'BIOPHOTO PIN=') !== false
        );

        if ($hasBiometricData) {
            // Process the biometric data
            BiometricEmployee::recordFingerprint($rows, $device);

            // Return success response
            return response("OK", 200)->header('Content-Type', 'text/plain');
        }

        Log::info('Attendance data received', ['request' => $request->all()]);

        BiometricEmployee::markAttendanceTodeviceAndApplication($rows, $device, $request);

        return response('OK', 200)->header('Content-Type', 'text/plain');
    }

    public function handshake(Request $request)
    {

        $sn = strtoupper($request->input('SN')) ?? ' ';

        if (!$sn) {
            Log::info('Handshake: Missing device SN:' . $sn);
            return response("ERROR: Missing device SN", 400)->header('Content-Type', 'text/plain');
        }

        $device = BiometricDevice::where('serial_number', $sn)->first();

        if (!$device) {
            Log::info('Handshake: Device not found:' . $sn);
            return response("Device not found", 404)->header('Content-Type', 'text/plain');
        }

        // update status device
        BiometricDevice::updateOrCreate(
            [
                'serial_number' => $sn,
                'company_id' => $device->company_id
            ],
            [
                'status' => 'online',
                'device_ip' => request()->ip(),
                'last_online' => now()
            ]
        );

        $timezoneToMinutes = BiometricCommands::timezoneToMinutes($device->company->timezone);

        $r = "GET OPTION FROM: {$sn}\r\n" .
            "Stamp=9999\r\n" .
            "OpStamp=" . time() . "\r\n" .
            "ErrorDelay=60\r\n" .
            "Delay=30\r\n" .
            "ResLogDay=18250\r\n" .
            "ResLogDelCount=10000\r\n" .
            "ResLogCount=50000\r\n" .
            "TransTimes=00:00;14:05\r\n" .
            "TransInterval=1\r\n" .
            "TransFlag=1111000000\r\n" .
            "TimeZone=" . $timezoneToMinutes . "\r\n" .
            "Realtime=1\r\n" .
            "Encrypt=0";

        Log::info('Handshake response: ' . $r);


        return response($r, 200)->header('Content-Type', 'text/plain');
    }

    /**
     * Handle device polling requests
     */
    public function handleGetRequest(Request $request)
    {
        $sn = strtoupper($request->get('SN'));

        // Lookup command for device
        $command = BiometricCommands::where('device_serial_number', $sn)->where('status', 'pending')->first();

        if ($command) {
            Log::info('Sending Command to Device:', ['command' => $command->command]);
            $command->update(['status' => 'sent', 'sent_at' => now()]); // Mark as sent
            return response($command->command, 200)->header('Content-Type', 'text/plain');
        }

        return response("OK", 200)->header('Content-Type', 'text/plain');
    }

    /**
     * Handle device command execution results
     */
    public function handleDeviceCommand(Request $request)
    {
        $sn = strtoupper($request->get('SN'));

        if (!$sn) {
            Log::info('Command execution failed: Missing device SN');
            return response('OK', 200)->header('Content-Type', 'text/plain');
        }

        $device = BiometricDevice::where('serial_number', $sn)->first();

        if (!$device) {
            Log::info('Command execution failed: Device not found');
            return response('OK', 200)->header('Content-Type', 'text/plain');
        }

        // Get the raw request body
        $rawBody = $request->getContent();

        // Parse the response body
        parse_str(str_replace("\n", "", $rawBody), $parsedResponse);

        // Extract command and return code
        $command = $parsedResponse['CMD'] ?? '';
        $returnCode = $parsedResponse['Return'] ?? '';

        // Log the parsed result
        Log::info('Parsed command result', [
            'command' => $command,
            'return_code' => $returnCode,
            'Parsed response: ' . json_encode($parsedResponse)
        ]);

        // Extract command ID from the parsed response
        $commandId = $parsedResponse['ID'] ?? '';

        if (!empty($commandId)) {
            $pendingCommand = BiometricCommands::where('command_id', $commandId)->first();

            if ($device->company_id != $pendingCommand->company_id) {
                Log::info('Command execution failed: Company ID mismatch');
                return response('OK', 200)->header('Content-Type', 'text/plain');
            }
        }

        // Return appropriate response based on the command result
        if ($returnCode == '0') {
            BiometricCommands::commandExecuted($pendingCommand, $device);
            return response('OK', 200)->header('Content-Type', 'text/plain');
        }


        BiometricCommands::commandFailed($pendingCommand);

        // Still return OK to the device but log the error
        Log::warning('Command execution failed', ['error_code' => $returnCode]);
        return response('OK', 200)->header('Content-Type', 'text/plain');
    }
}
