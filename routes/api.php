<?php

use App\Http\Controllers\DoorController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/openDoor', [DoorController::class, 'open']);


Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('refresh', [AuthController::class, 'refreshToken']);
    
    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
});