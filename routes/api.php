<?php
use App\Http\Controllers\DoorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserDeviceController;
// use App\Http\Controllers\DeviceController; // Uklonjeno jer ne postoji
use Illuminate\Support\Facades\Route;




Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('refresh', [AuthController::class, 'refreshToken']);
    
    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

Route::middleware('auth:api')->group(function () {
    Route::get('user/{id}/devices', [UserDeviceController::class, 'getUserDevices']);
    Route::post('/openDoor', [DoorController::class, 'open']);
    Route::get('user/{user}/door-unlocks', [DoorController::class, 'userDoorUnlocks']);
});

Route::middleware(['auth:api', 'isAdmin'])->group(function () {
    Route::post('user/{user}/assign-device/{device}', [UserDeviceController::class, 'assignDevice']);
    Route::delete('user/{user}/remove-device/{device}', [UserDeviceController::class, 'removeDevice']);
    Route::get('/recentDoorUnlock', [DoorController::class, 'recentDoorUnlock']);
});
