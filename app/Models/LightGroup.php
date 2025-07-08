<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LightGroup extends Model
{
    protected $table = 'light_groups';

    protected $fillable = [
        'device_id', 'name', 'group_index', 'status'
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
} 