<?php

namespace Modules\Biometric\Http\Controllers;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;

use Modules\Biometric\Entities\BiometricEmployee;
use Modules\Biometric\Entities\BiometricCommands;

use Modules\Biometric\Entities\BiometricAttendance;
use Illuminate\Http\Request;
use Modules\Biometric\Entities\BiometricDevice;
use App\Models\User;

class BiometricEmployeeController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'biometric::app.menu.deviceEmployees';

        $this->middleware(function ($request, $next) {
            if (!in_array('biometric', $this->user->modules) && user()->permission('manage_biometric_settings') != 'none') {
                abort(403, __('messages.permissionDenied'));
            }

            return $next($request);
        });
    }


    public function index()
    {
        $viewPermission = user()->permission('view_employees');

        abort_403(!in_array($viewPermission, ['all', 'added', 'owned', 'both']));

        $this->employees = User::withRole('employee')
            ->join('employee_details', 'employee_details.user_id', '=', 'users.id')
            ->leftJoin('biometric_employees', 'users.id', '=', 'biometric_employees.user_id')
            ->leftJoin('designations', 'employee_details.designation_id', '=', 'designations.id')
            ->select(
                'users.id',
                'users.company_id',
                'users.name',
                'users.email',
                'users.created_at',
                'users.image',
                'designations.name as designation_name',
                'users.status',
                'employee_details.employee_id',
                'biometric_employees.biometric_employee_id',
                'biometric_employees.has_fingerprint',
                'biometric_employees.force_biometric_clockin'
            )->get();


        // Pass devices to the view to check if any exist
        $this->devices = \Modules\Biometric\Entities\BiometricDevice::where('company_id', company()->id)->get();

        return view('biometric::employee.edit', $this->data);
    }




    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $biometricEmployeeIds = $request->biometric_employee_id;

        foreach ($biometricEmployeeIds as $userId => $biometricEmployeeId) {

            if (!empty($biometricEmployeeId)) {
                \Log::info('$request->force_biometric_clockin[$userId]' . $userId . ' :' . $request->force_biometric_clockin[$userId]);
                BiometricEmployee::updateOrCreate(
                    ['user_id' => $userId],
                    [
                        'company_id' => $this->user->company_id,
                        'biometric_employee_id' => $biometricEmployeeId,
                        'force_biometric_clockin' => boolval($request->force_biometric_clockin[$userId])
                    ]
                );

                BiometricAttendance::where('employee_id', $biometricEmployeeId)
                    ->where('company_id', $this->user->company_id)
                    ->update([
                        'user_id' => $userId,
                    ]);
            }
        }

        return Reply::success(__('messages.recordSaved'));
    }

    public function getEmployeesToSync()
    {
        $employees = User::withRole('employee')
            ->join('employee_details', 'employee_details.user_id', '=', 'users.id')
            ->leftJoin('biometric_employees', 'users.id', '=', 'biometric_employees.user_id')
            ->leftJoin('designations', 'employee_details.designation_id', '=', 'designations.id')
            ->where('users.status', 'active')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.image',
                'employee_details.employee_id',
                'biometric_employees.biometric_employee_id'
            )->get();

        $data = $employees->map(function ($employee) {
            return [
                'id' => $employee->id,
                'name' => $employee->name,
                'email' => $employee->email,
                'image_url' => $employee->image_url,
                'employee_id' => $employee->employee_id ?? '--',
                'biometric_id' => $employee->biometric_employee_id ?? '--',
                'is_configured' => !empty($employee->biometric_employee_id)
            ];
        });

        return Reply::dataOnly(['data' => $data]);
    }

    /**
     * Remove employee from biometric device
     */
    public function removeFromDevice($id)
    {
        $biometricEmployee = BiometricEmployee::where('user_id', $id)
            ->where('company_id', $this->user->company_id)
            ->first();

        $devices = BiometricDevice::all();

        foreach ($devices as $device) {
            if ($biometricEmployee) {

                $biometricEmployee->delete();
                // Create a pending command to remove the employee from physical devices
                $biometricCommand = BiometricCommands::create([
                    'company_id' => company()->id,
                    'type' => 'DELETEUSER',
                    'command_id' => 'TEMP-' . time(),
                    'user_id' => $id,
                    'employee_id' => $biometricEmployee->biometric_employee_id,
                    'device_serial_number' => $device->serial_number,
                    'command' => 'TEMPCOMMAND-' . time(),
                    'status' => 'pending'
                ]);

                // Update the command_id with the actual database ID
                $biometricCommand->update([
                    'command_id' => 'DELETEUSER-' . $biometricCommand->id,
                    'command' => BiometricCommands::deleteUserCommand($biometricCommand->id, $biometricEmployee->biometric_employee_id),
                ]);
            }
        }

        return Reply::success(__('biometric::app.employeeRemovedFromDevice'));
    }


    public function getEmployeeInfo($id = null)
    {

        if ($id) {
            $biometricEmployees = BiometricEmployee::where('user_id', $id)->get();
        } else {
            $biometricEmployees = BiometricEmployee::all();
        }


        $command = [];

        $devices = BiometricDevice::all();

        foreach ($devices as $device) {
            foreach ($biometricEmployees as $employee) {
                $biometricCommand = BiometricCommands::create([
                    'company_id' => company()->id,
                    'type' => 'QUERYUSER',
                    'command_id' => 'TEMP-' . time(),
                    'device_serial_number' => $device->serial_number,
                    'user_id' => $employee->user_id,
                    'employee_id' => $employee->biometric_employee_id,
                    'status' => 'pending'
                ]);

                // Update the command_id with the actual database ID
                $biometricCommand->update([
                    'command_id' => 'QUERYUSER-' . $biometricCommand->id,
                    'command' => BiometricCommands::queryUserCommand($biometricCommand->id, $employee->biometric_employee_id),
                ]);

                $command[] = $biometricCommand;
            }
        }

        return Reply::successWithData(__('biometric::app.fetchAllBiometricDataSuccess'), ['data' => $command]);
    }
}
