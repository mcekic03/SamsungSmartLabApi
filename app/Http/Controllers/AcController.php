<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use App\Tuya;

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
        return $this->tuyaOn();
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
        return $this->tuyaOff();
    }

    public function tuyaOn()
    {
        $device = Device::find(2);
        $user = auth('api')->user();
        if (!$device || $device->type !== 'ac') {
            return response()->json(['error' => 'AC uređaj nije pronađen.'], 404);
        }
        if (!$user->devices()->where('devices.id', 2)->exists()) {
            return response()->json(['error' => 'Nemate pravo da upravljate ovim uređajem.'], 403);
        }
        $body = [
            'commands' => [
                [
                    'code' => 'power',
                    'value' => true
                ]
            ]
        ];
        $result = Tuya::controlDevice($device->device_id, $body);
        return response()->json($result);
    }

    public function tuyaOff()
    {
        $device = Device::find(2);
        $user = auth('api')->user();
        if (!$device || $device->type !== 'ac') {
            return response()->json(['error' => 'AC uređaj nije pronađen.'], 404);
        }
        if (!$user->devices()->where('devices.id', 2)->exists()) {
            return response()->json(['error' => 'Nemate pravo da upravljate ovim uređajem.'], 403);
        }
        $body = [
            'commands' => [
                [
                    'code' => 'power',
                    'value' => false
                ]
            ]
        ];
        $result = Tuya::controlDevice($device->device_id, $body);
        return response()->json($result);
    }
} 