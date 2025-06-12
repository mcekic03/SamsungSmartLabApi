<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
     protected $table = 'devices';

    protected $fillable = ['name', 'icon', 'type', 'status'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function unlockLogs()
{
    return $this->hasMany(DeviceUnlockLog::class);
}

}
