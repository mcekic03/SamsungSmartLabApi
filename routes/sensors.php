<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SenzorController;


//odsek nis
Route::post('/senzori', [SenzorController::class, 'store']);
//odsek pirot
Route::post('/senzorAppsLab', [SenzorController::class, 'store']);
//odsek vranje
Route::post('/odsekVranje', [SenzorController::class, 'store']);

Route::get('/GetSenzorData', [SenzorController::class, 'getLastEntry']);
