<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OutdoorSensor extends Model
{
    protected $table = 'outdoor_sensors';

    protected $fillable = [
        'esp8266id',
        'software_version',
        'PMS_P0',
        'PMS_P1',
        'PMS_P2',
        'BME280_temperature',
        'BME280_pressure',
        'BME280_humidity',
        'samples',
        'min_micro',
        'max_micro',
        'interval',
        'signal',
    ];

    public $timestamps = false; // pošto koristiš `timestamp` kolonu ručno

     public static function getLastEntry($id)
    {
        $row = DB::table('outdoor_sensors')
            ->where('esp8266id', $id)
            ->orderByDesc('timestamp')
            ->first();

        if (!$row) {
            return null;
        }

        // Konvertovanje timestamp u lokalno vreme (Beograd)
        // Samo formatuj bez konverzije
        $timestampLocal = Carbon::parse($row->timestamp)
        ->format('Y-m-d H:i:s');

        // Određivanje lokacije po id-u
        $locations = [
            '16160069' => 'АТВСС Одсек Пирот',
            '15139426' => 'АТВСС Одсек Врање',
        ];

        $location = $locations[$id] ?? 'АТВСС Одсек Ниш';

        return [
            'location'    => $location,
            'id'          => $row->esp8266id,
            'timestamp'   => $timestampLocal,
            'pm1'         => $row->PMS_P0,
            'pm25'        => $row->PMS_P1,
            'pm10'        => $row->PMS_P2,
            'temperature' => $row->BME280_temperature,
            'pressure'    => $row->BME280_pressure,
            'humidity'    => $row->BME280_humidity,
        ];
    }
}
