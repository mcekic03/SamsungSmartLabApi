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

    public function recentDoorUnlock(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');

        $query = \App\Models\DeviceUnlockLog::with('user');

        if ($from) {
            $query->where('unlocked_at', '>=', $from);
        }
        if ($to) {
            $query->where('unlocked_at', '<=', $to);
        }

        $logs = $query->orderBy('unlocked_at', 'desc')
            ->get()
            ->map(function($log) {
                return [
                    'first_name' => $log->user->FirstName,
                    'last_name'  => $log->user->LastName,
                    'email'      => $log->user->email,
                    'unlocked_at'=> $log->unlocked_at,
                ];
            });

        return response()->json($logs);
    }

    public function userDoorUnlocks($userId, Request $request)
    {
        $authUser = auth('api')->user();
        // Ako nije admin, može da vidi samo svoju istoriju
        if ($authUser->role !== 'admin' && $authUser->id != $userId) {
            return response()->json(['error' => 'Nemate dozvolu za pristup.'], 403);
        }

        $from = $request->query('from');
        $to = $request->query('to');

        $query = \App\Models\DeviceUnlockLog::where('user_id', $userId);

        if ($from) {
            $query->where('unlocked_at', '>=', $from);
        }
        if ($to) {
            $query->where('unlocked_at', '<=', $to);
        }

        $logs = $query->orderBy('unlocked_at', 'desc')->pluck('unlocked_at');

        return response()->json($logs);
    }
}

