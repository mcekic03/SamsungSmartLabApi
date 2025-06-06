<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OutdoorSensor;
use Illuminate\Support\Facades\Log;
use App\Models\IndoorSensor;
use Illuminate\Support\Facades\Validator;

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
        try {
            // Validacija ulaznih podataka
            $validator = Validator::make($request->all(), [
                'senzori' => 'required|array',
                'senzori.*.id' => 'required|integer',
                'senzori.*.t' => 'required|numeric',
                'senzori.*.CO' => 'required|numeric',
                'senzori.*.p' => 'required|numeric',
                'senzori.*.rh' => 'required|numeric',
                'timestamp' => 'required|date'
            ]);

            // Provera validacije
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Neispravni podaci',
                    'errors' => $validator->errors()
                ], 400);
            }

            // Dobavljanje svih podataka iz zahteva
            $data = $request->all();
            $timestamp = \Carbon\Carbon::parse($data['timestamp']);

            // Čuvanje svakog senzora
            $savedSensors = [];
            foreach ($data['senzori'] as $sensorData) {
                $indoorSensor = IndoorSensor::create([
                    'sensor_id' => $sensorData['id'],
                    'temperature' => $sensorData['t'],
                    'co_level' => $sensorData['CO'],
                    'pressure' => $sensorData['p'],
                    'humidity' => $sensorData['rh'],
                    'sensor_timestamp' => $timestamp
                ]);

                $savedSensors[] = $indoorSensor->id;
            }

            // Detaljno logovanje podataka
            Log::info('Primljeni podaci za unutrašnje senzore:', [
                'sensor_count' => count($data['senzori']),
                'saved_sensor_ids' => $savedSensors,
                'timestamp' => $timestamp,
                'received_at' => now()
            ]);

            // Vraćanje odgovora
            return response()->json([
                'status' => 'success',
                'message' => 'Podaci su uspešno sačuvani.',
                'saved_count' => count($savedSensors),
                'saved_ids' => $savedSensors,
                'received_at' => now()
            ], 201);

        } catch (\Exception $e) {
            // Logovanje greške
            Log::error('Greška pri čuvanju podataka unutrašnjih senzora:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Vraćanje greške
            return response()->json([
                'status' => 'error',
                'message' => 'Došlo je do greške pri obradi podataka.',
                'error_details' => $e->getMessage()
            ], 500);
        }
    }

    public function getLatestTemperature($sensorId)
    {
        try {
            // Pronalaženje poslednjeg unosa za specifični senzor
            $latestSensor = IndoorSensor::where('sensor_id', $sensorId)
                ->orderBy('sensor_timestamp', 'desc')
                ->first();

            // Provera da li postoji unos
            if (!$latestSensor) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Nema podataka za senzor sa ID: $sensorId"
                ], 404);
            }

            // Vraćanje odgovora
            return response()->json([
                'status' => 'success',
                'sensor_id' => $latestSensor->sensor_id,
                'temperature' => $latestSensor->temperature,
                'timestamp' => $latestSensor->sensor_timestamp,
                'received_at' => now()
            ]);

        } catch (\Exception $e) {
            // Logovanje greške
            Log::error('Greška pri dobijanju temperature:', [
                'sensor_id' => $sensorId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Vraćanje greške
            return response()->json([
                'status' => 'error',
                'message' => 'Došlo je do greške pri preuzimanju temperature.',
                'error_details' => $e->getMessage()
            ], 500);
        }
    }


}
