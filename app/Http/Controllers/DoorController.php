<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DoorController extends Controller
{
    public function open(Request $request)
    {
        // Slanje POST zahteva ka drugom serveru
        $response = Http::post('http://160.99.40.144:3500/execute', [
            'command' => 'run_function'
        ]);

        // Provera da li je zahtev uspešan
        if ($response->successful()) {
            return response()->json($response->json(), 200);
        }

        // Ako nije uspešan, vrati grešku klijentu
        return response()->json([
            'error' => 'Greška prilikom otvaranja vrata',
            'details' => $response->body()
        ], $response->status());
    }
}

