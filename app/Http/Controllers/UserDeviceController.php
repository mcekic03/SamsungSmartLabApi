<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Device;

class UserDeviceController extends Controller
{
    public function getUserDevices($id)
    {
        // Validacija postoji li korisnik sa tim ID-jem
        $user = User::with('devices')->find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Vrati uređaje kao JSON
        return response()->json($user->devices);
    }

    public function getDevicesWithUsers()
    {
        $devices = Device::with(['users:id,FirstName,LastName'])->get();

        return response()->json($devices);
    }

    public function assignDevice($userId, $deviceId)
    {
        $user = \App\Models\User::find($userId);
        $device = \App\Models\Device::find($deviceId);

        if (!$user || !$device) {
            return response()->json(['error' => 'Korisnik ili uređaj nije pronađen.'], 404);
        }

        // Proveri da li je već dodeljen
        if ($user->devices()->where('devices.id', $deviceId)->exists()) {
            return response()->json(['message' => 'Uređaj je već dodeljen korisniku.'], 200);
        }

        $user->devices()->attach($deviceId);

        return response()->json(['message' => 'Uređaj uspešno dodeljen korisniku.']);
    }

    public function removeDevice($userId, $deviceId)
    {
        $user = \App\Models\User::find($userId);
        $device = \App\Models\Device::find($deviceId);

        if (!$user || !$device) {
            return response()->json(['error' => 'Korisnik ili uređaj nije pronađen.'], 404);
        }

        // Proveri da li je uređaj dodeljen korisniku
        if (!$user->devices()->where('devices.id', $deviceId)->exists()) {
            return response()->json(['message' => 'Uređaj nije dodeljen korisniku.'], 200);
        }

        $user->devices()->detach($deviceId);

        return response()->json(['message' => 'Uređaj je uklonjen korisniku.']);
    }
}
