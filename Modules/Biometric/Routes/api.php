<?php

use Illuminate\Support\Facades\Route;
use Modules\Biometric\Http\Controllers\ZKTecoController;

// ZKTeco device routes


Route::get('/iclock/cdata', [ZKTecoController::class, 'handshake']);

Route::get('/iclock/test', [ZKTecoController::class, 'test']);
Route::post('/iclock/cdata', [ZKTecoController::class, 'handleAttendanceData']);
Route::post('/iclock/devicecmd', [ZKTecoController::class, 'handleDeviceCommand']);
Route::get('/iclock/getrequest', [ZKTecoController::class, 'handleGetRequest']);
Route::get('/iclock/ping', [ZKTecoController::class, 'handlePing']);
