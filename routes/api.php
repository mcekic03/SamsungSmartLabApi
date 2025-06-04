<?php

use App\Http\Controllers\DoorController;
use Illuminate\Support\Facades\Route;

Route::post('/openDoor', [DoorController::class, 'open']);
