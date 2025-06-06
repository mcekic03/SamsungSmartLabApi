<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class IndoorSensor extends Model
{
    use HasFactory;

    protected $table = 'indoor_sensors';

    protected $fillable = [
        'sensor_id',
        'temperature',
        'co_level',
        'pressure',
        'humidity',
        'sensor_timestamp'
    ];

    protected $casts = [
        'temperature' => 'float',
        'co_level' => 'float',
        'pressure' => 'float',
        'humidity' => 'float',
        'sensor_timestamp' => 'datetime'
    ];
}

