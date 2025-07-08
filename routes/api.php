<?php
use App\Http\Controllers\DoorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserDeviceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TuyaController;
use App\Http\Controllers\AcController;
use App\Http\Controllers\DeviceController;
use Illuminate\Support\Facades\Route;




Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('refresh', [AuthController::class, 'refreshToken']);
    Route::get('/tuya/token', [TuyaController::class, 'getTokenSimple']);
    
    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

Route::middleware('auth:api')->group(function () {
    Route::get('user/{id}/devices', [UserDeviceController::class, 'getUserDevices']);
    Route::post('/openDoor', [DoorController::class, 'open']);
    Route::get('user/{user}/door-unlocks', [DoorController::class, 'userDoorUnlocks']);
    Route::post('ac/on', [AcController::class, 'turnOn']);
    Route::post('ac/off', [AcController::class, 'turnOff']);
});

Route::middleware(['auth:api', 'isAdmin'])->group(function () {
    Route::post('user/{user}/assign-device/{device}', [UserDeviceController::class, 'assignDevice']);
    Route::delete('user/{user}/remove-device/{device}', [UserDeviceController::class, 'removeDevice']);
    Route::get('/recentDoorUnlock', [DoorController::class, 'recentDoorUnlock']);
    Route::get('/allUsers', [UserController::class, 'allUsers']);
    Route::get('/allDevices', [DeviceController::class, 'allDevices']);
});
