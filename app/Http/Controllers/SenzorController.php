<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OutdoorSensor;

class SenzorController extends Controller
{
    public function store(Request $request)
    {
        try {
            $data = $request->all();

            // Inicijalizuj sve vrednosti
            $values = [
                'PMS_P0' => null,
                'PMS_P1' => null,
                'PMS_P2' => null,
                'BME280_temperature' => null,
                'BME280_pressure' => null,
                'BME280_humidity' => null,
                'samples' => null,
                'min_micro' => null,
                'max_micro' => null,
                'interval' => null,
                'signal' => null,
            ];

            // Popuni iz `sensordatavalues`
            foreach ($data['sensordatavalues'] as $sensor) {
                if (array_key_exists($sensor['value_type'], $values)) {
                    $values[$sensor['value_type']] = $sensor['value'];
                }
            }

            // Konverzija pritiska u hPa (kao što si imao u Express.js)
            if (!is_null($values['BME280_pressure'])) {
                $values['BME280_pressure'] = number_format($values['BME280_pressure'] / 100, 3, '.', '');
            }

            // Upis u bazu
            OutdoorSensor::create([
                'esp8266id' => $data['esp8266id'],
                'software_version' => $data['software_version'],
                'PMS_P0' => $values['PMS_P0'],
                'PMS_P1' => $values['PMS_P1'],
                'PMS_P2' => $values['PMS_P2'],
                'BME280_temperature' => $values['BME280_temperature'],
                'BME280_pressure' => $values['BME280_pressure'],
                'BME280_humidity' => $values['BME280_humidity'],
                'samples' => $values['samples'],
                'min_micro' => $values['min_micro'],
                'max_micro' => $values['max_micro'],
                'interval' => $values['interval'],
                'signal' => $values['signal'],
                // 'timestamp' se automatski popunjava sa useCurrent()
            ]);

            return response()->json(['status' => 'success', 'message' => 'Podaci uspešno upisani'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    public function getLastEntry()
    {
        try {
            $ATVSSOdsekNis = OutdoorSensor::getLastEntry('16160069');
            $SamsungAppsLab = OutdoorSensor::getLastEntry('9091332');
            $ATVSSOdsekVranje = OutdoorSensor::getLastEntry('15139426');

            // Ovde pretpostavljam da ces napraviti metodu procenti() u OutdoorSensor modelu ili nekom servisu
            $poruka = "Михајло Цекић";

            return response()->json([
                $ATVSSOdsekNis,
                $SamsungAppsLab,
                $poruka,
                $ATVSSOdsekVranje
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Došlo je do greške pri dobijanju poslednjeg unosa.'
            ], 500);
        }
    }

    public function getNis()
    {
        try {
            $ATVSSOdsekNis = OutdoorSensor::getLastEntry('16160069');

            // Ovde pretpostavljam da ces napraviti metodu procenti() u OutdoorSensor modelu ili nekom servisu
            $poruka = "Михајло Цекић";

            return response()->json([
                $ATVSSOdsekNis
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Došlo je do greške pri dobijanju poslednjeg unosa.'
            ], 500);
        }
    }

    public function inDoorStore(Request $request)
    {
        // Dobavljanje svih podataka iz zahteva
        $data = $request->all();

        // Logovanje podataka u JSON formatu
        Log::info($data);

        // Opcionalno: vraćanje odgovora
        return response()->json(['status' => 'Podaci su uspešno zabeleženi.']);
    }


}
