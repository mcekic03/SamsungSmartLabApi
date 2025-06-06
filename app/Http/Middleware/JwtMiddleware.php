<?php
// app/Http/Middleware/JwtMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtMiddleware
{
    public function handle($request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['error' => 'Korisnik nije pronađen'], 404);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token nije valjan'], 401);
        }

        return $next($request);
    }
}