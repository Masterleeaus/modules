<?php

namespace Modules\Biometric\Http\Controllers;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use Modules\Biometric\Entities\BiometricDevice;
use Modules\Biometric\Http\Requests\BiometricDeviceStore;
use App\Models\User;
use Modules\Biometric\Entities\BiometricCommands;

class BiometricDeviceController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'biometric::app.menu.devices';

        $this->middleware(function ($request, $next) {
            abort_403(!in_array('biometric', $this->user->modules) && user()->permission('manage_biometric_settings') != 'none');
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        BiometricDevice::where('last_online', '<', now()->subMinutes(20))
            ->update(['status' => 'offline']);

        $this->biometricDevice = BiometricDevice::all();


        return view('biometric::devices.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->pageTitle = __('biometric::app.addBiometricDevice');

        $this->view = 'biometric::devices.ajax.create';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('biometric::devices.create', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BiometricDeviceStore $request)
    {
        $device = new BiometricDevice();
        $device->company_id = company()->id;
        $device->device_name = $request->device_name;
        $device->serial_number = strtoupper($request->serial_number);
        $device->status = 'pending'; // Set initial status as 'pending'
        $device->save();

        return Reply::successWithData(__('messages.recordSaved'), ['redirectUrl' => route('biometric-devices.index')]);
    }

    public function syncEmployees()
    {
        // Get all active devices
        $devices = BiometricDevice::where('status', '!=', 'offline')->get();

        if ($devices->isEmpty()) {
            return Reply::error(__('biometric::app.noActiveDevices'));
        }

        $this->employees = User::withRole('employee')
            ->join('employee_details', 'employee_details.user_id', '=', 'users.id')
            ->leftJoin('biometric_employees', 'users.id', '=', 'biometric_employees.user_id')
            ->select(
                'users.id',
                'users.company_id',
                'users.name',
                'employee_details.employee_id',
            )
            ->whereIn('users.id', request()->employee_ids)
            ->get();

        foreach ($devices as $device) {
            foreach ($this->employees as $employee) {
                // Create the command record first
                $biometricCommand = BiometricCommands::create([
                    'company_id' => company()->id,
                    'type' => 'CREATEUSER',
                    'command_id' => 'TEMP-' . time(), // Temporary ID
                    'user_id' => $employee->id,
                    'employee_id' => $employee->employee_id,
                    'device_serial_number' => $device->serial_number,
                    'command' => 'TEMPCOMMAND-' . time(),
                    'status' => 'pending'
                ]);

                // Update the command_id with the actual database ID
                $biometricCommand->update([
                    'command_id' => 'CREATEUSER-' . $biometricCommand->id,
                    'command' => BiometricCommands::createUserCommand($biometricCommand->id, $employee->employee_id, $employee->name),
                ]);
            }
        }

        return Reply::success(__('biometric::app.employeesSyncInitiated', ['pendingCommandsUrl' => route('biometric-devices.commands')]));
    }

    /**
     * Display pending commands
     */
    public function commands()
    {
        $this->pageTitle = __('biometric::app.menu.commands');

        $this->pendingCommands = BiometricCommands::with(['device', 'user'])
            ->where('company_id', company()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('biometric::commands.index', $this->data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $device = BiometricDevice::findOrFail($id);
        $device->delete();

        return Reply::success(__('messages.deleteSuccess'));
    }
}
