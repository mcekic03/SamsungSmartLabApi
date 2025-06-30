<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;

class AcController extends Controller
{
    public function turnOn()
    {
        $device = Device::find(2);
        $user = auth('api')->user();
        if (!$device || $device->type !== 'ac') {
            return response()->json(['error' => 'AC uređaj nije pronađen.'], 404);
        }
        if (!$user->devices()->where('devices.id', 2)->exists()) {
            return response()->json(['error' => 'Nemate pravo da upravljate ovim uređajem.'], 403);
        }
        $device->status = 'on';
        $device->save();
        return response()->json(['message' => 'AC uređaj je uključen.', 'device' => $device]);
    }

    public function turnOff()
    {
        $device = Device::find(2);
        $user = auth('api')->user();
        if (!$device || $device->type !== 'ac') {
            return response()->json(['error' => 'AC uređaj nije pronađen.'], 404);
        }
        if (!$user->devices()->where('devices.id', 2)->exists()) {
            return response()->json(['error' => 'Nemate pravo da upravljate ovim uređajem.'], 403);
        }
        $device->status = 'off';
        $device->save();
        return response()->json(['message' => 'AC uređaj je isključen.', 'device' => $device]);
    }
} 