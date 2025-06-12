<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Device;
use App\Models\DeviceUnlockLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DoorController extends Controller
{
    public function open(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id'
        ]);

        $userId = $request->input('user_id');
        $deviceId = 1; // Pretpostavljam da otvaraš bravu sa ID 1

        // Pronađi korisnika
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'Korisnik nije pronađen.'], 404);
        }

        // Proveri da li korisnik ima pristup ovom uređaju
        if (!$user->devices()->where('devices.id', $deviceId)->exists()) {
            return response()->json(['error' => 'Nemate pristup ovom uređaju.'], 403);
        }

        // Pronađi uređaj
        $device = Device::find($deviceId);
        if (!$device) {
            return response()->json(['error' => 'Uređaj nije pronađen.'], 404);
        }

        // Pošalji POST zahtev ka drugom serveru za otvaranje brave
        $response = Http::post('http://160.99.40.144:3500/execute', [
            'command' => 'run_function'
        ]);

        // Ako je uspešan
        if ($response->successful()) {

            // Ažuriraj status uređaja
            $device->status = 'unlocked';
            $device->save();

            // Upisi log
            DeviceUnlockLog::create([
                'user_id' => $userId,
                'device_id' => $deviceId,
                'unlocked_at' => now(),
            ]);

            return response()->json([
                'message' => 'Vrata su uspešno otključana.',
                'device' => $device,
                'log' => 'Upisano u log.',
                'remote_response' => $response->json(),
            ], 200);
        }

        return response()->json([
            'error' => 'Greška prilikom otvaranja vrata.',
            'details' => $response->body()
        ], $response->status());
    }
}

