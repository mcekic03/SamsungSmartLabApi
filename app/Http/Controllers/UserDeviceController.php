<?php

namespace App\Http\Controllers;

use App\Models\User;

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
}
