<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    protected $fillable = [
        'FirstName','LastName','role', 'email', 'password', 'remember_token'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed', // Laravel 10+
    ];


    // JWT metode
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
         return [
            'first_name' => $this->FirstName,
            'last_name' => $this->LastName,
            'role' => $this->role,
        ];
    }

    
    public function devices()
    {
        return $this->belongsToMany(Device::class);
    }

    public function unlockLogs()
{
    return $this->hasMany(DeviceUnlockLog::class);
}

}