<?php
// app/Models/RefreshToken.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RefreshToken extends Model
{
    protected $fillable = ['user_id', 'token', 'expires_at'];
    
    protected $dates = ['expires_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    public static function createForUser($userId, $rememberMe = false)
    {
        $expiresAt = $rememberMe 
            ? Carbon::now()->addDays(30)  // 30 dana ako je "zapamti me"
            : Carbon::now()->addDays(7);  // 7 dana inače

        return self::create([
            'user_id' => $userId,
            'token' => bin2hex(random_bytes(32)),
            'expires_at' => $expiresAt,
        ]);
    }
}