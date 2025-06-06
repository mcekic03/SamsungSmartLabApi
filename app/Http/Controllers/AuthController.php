<?php
// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RefreshToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'remember_me' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $credentials = $request->only('email', 'password');
        $rememberMe = $request->get('remember_me', false);

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Neispravni podaci'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Greška pri kreiranju tokena'], 500);
        }

        $user = auth('api')->user();
        
        // Napravi refresh token
        $refreshToken = RefreshToken::createForUser($user->id, $rememberMe);

        return response()->json([
            'access_token' => $token,
            'refresh_token' => $refreshToken->token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'user' => [
                'id' => $user->id,
                'first_name' => $user->FirstName,
                'last_name' => $user->LastName,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'role' => $user->role,
            ]
        ]);
    }

    public function refreshToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'refresh_token' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $refreshToken = RefreshToken::where('token', $request->refresh_token)->first();

        if (!$refreshToken || $refreshToken->isExpired()) {
            return response()->json(['error' => 'Nevažeći refresh token'], 401);
        }

        $user = $refreshToken->user;
        
        try {
            // Generiši novi access token
            $newAccessToken = JWTAuth::fromUser($user);
            
            // Opciono: generiši novi refresh token
            $refreshToken->delete();
            $newRefreshToken = RefreshToken::createForUser($user->id, true);

            return response()->json([
                'access_token' => $newAccessToken,
                'refresh_token' => $newRefreshToken->token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->FirstName,
                    'last_name' => $user->LastName,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'role' => $user->role,
                ]
            ]);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Greška pri kreiranju tokena'], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            // Obriši refresh token ako je poslat
            if ($request->has('refresh_token')) {
                RefreshToken::where('token', $request->refresh_token)->delete();
            }

            // Invalidaj JWT token
            JWTAuth::invalidate(JWTAuth::getToken());
            
            return response()->json(['message' => 'Uspešno odjavljen']);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Greška pri odjavi'], 500);
        }
    }

    public function me()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['error' => 'Korisnik nije pronađen'], 404);
            }

            return response()->json([
                'id' => $user->id,
                'first_name' => $user->FirstName,
                'last_name' => $user->LastName,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'role' => $user->role,
            ]);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token nije valjan'], 401);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'FirstName' => 'required|string|max:255',
            'LastName' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'nullable|in:admin,user,moderator'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'FirstName' => $request->FirstName,
            'LastName' => $request->LastName,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'user',
        ]);

        return response()->json([
            'message' => 'Korisnik uspešno kreiran',
            'user' => [
                'id' => $user->id,
                'first_name' => $user->FirstName,
                'last_name' => $user->LastName,
                'email' => $user->email,
                'role' => $user->role,
            ]
        ], 201);
    }
}