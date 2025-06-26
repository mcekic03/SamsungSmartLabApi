<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         $user = JWTAuth::parseToken()->authenticate();

        if (!$user || $user->role !== 'admin') {
            return response()->json(['error' => 'Nemate dozvolu za pristup.'], 403);
        }

        return $next($request);
    }
}
