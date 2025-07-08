<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\LightGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;

class DeviceController extends Controller
{
    public function allDevices()
    {
        $devices = Device::all();
        return response()->json($devices);
    }

    // Pomoćna funkcija za ažuriranje statusa svetala i uređaja
    private function azurirajStatusIzEsp32(Device $device)
    {
        $client = new Client();
        $token = 'u93KxVf7zP1mLDqG9bT6WnYcRaE0sMjXkAvhZ2oFgtUBeiNlCqHdYrT5pJ8OwLx1';
        $ip = '160.99.40.140';
        $url = "http://$ip/statusLights?token=$token";
        $response = $client->get($url);
        $statusi = json_decode($response->getBody(), true);

        $imaUpaljenih = false;
        foreach ($device->lightGroups as $group) {
            $relayKey = 'relay' . $group->group_index;
            $relayStatus = isset($statusi[$relayKey]) ? $statusi[$relayKey] : 'OFF';
            $group->status = ($relayStatus === 'ON') ? 'on' : 'off';
            $group->save();
            if ($relayStatus === 'ON') {
                $imaUpaljenih = true;
            }
        }
        $device->status = $imaUpaljenih ? 'on' : 'off';
        $device->save();
    }

    // Funkcija za paljenje svih svetala
    public function upaliSveSvetla($deviceId)
    {
        $device = Device::findOrFail($deviceId);
        $client = new Client();
        $token = 'u93KxVf7zP1mLDqG9bT6WnYcRaE0sMjXkAvhZ2oFgtUBeiNlCqHdYrT5pJ8OwLx1';
        $ip = '160.99.40.140';
        $url = "http://$ip/turnOnAll?token=$token";
        $client->get($url);
        $this->azurirajStatusIzEsp32($device);
        return response()->json(['message' => 'Sva svetla upaljena i status ažuriran.']);
    }

    // Funkcija za gašenje svih svetala
    public function ugasiSveSvetla($deviceId)
    {
        $device = Device::findOrFail($deviceId);
        $client = new Client();
        $token = 'u93KxVf7zP1mLDqG9bT6WnYcRaE0sMjXkAvhZ2oFgtUBeiNlCqHdYrT5pJ8OwLx1';
        $ip = '160.99.40.140';
        $url = "http://$ip/turnOffAll?token=$token";
        $client->get($url);
        $this->azurirajStatusIzEsp32($device);
        return response()->json(['message' => 'Sva svetla ugašena i status ažuriran.']);
    }

    // Funkcija za toggle pojedinačne svetlosne grupe
    public function toggleSvetloPoGrupi($deviceId, $groupIndex)
    {
        $device = Device::findOrFail($deviceId);
        $group = $device->lightGroups()->where('group_index', $groupIndex)->firstOrFail();
        $client = new Client();
        $token = 'u93KxVf7zP1mLDqG9bT6WnYcRaE0sMjXkAvhZ2oFgtUBeiNlCqHdYrT5pJ8OwLx1';
        $ip = '160.99.40.140';
        $url = "http://$ip/toggleRelay{$groupIndex}?token=$token";
        $client->get($url);
        $this->azurirajStatusIzEsp32($device);
        return response()->json(['message' => "Svetlosna grupa $groupIndex togglovana i status ažuriran."]);
    }

    // Ruta za status svetala iz baze
    public function statusSvetala($deviceId)
    {
        $device = Device::with('lightGroups')->findOrFail($deviceId);
        $response = [
            'device' => $device->status,
        ];
        foreach ($device->lightGroups as $group) {
            $response['group_' . $group->group_index] = $group->status;
        }
        return response()->json($response);
    }
} 