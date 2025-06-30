<?php

namespace App;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class Tuya
{
    public static function getTokenSimple()
    {
        $clientId = 'jjavkaktjpjv7qycft4d';
        $clientSecret = '638a4e68c7db4f019f495358aea3453b';
        $t = round(microtime(true) * 1000);
        
        $method = 'GET';
        $url = '/v1.0/token?grant_type=1';
        $body = '';
        $bodyHash = hash('sha256', $body);
        
        $stringToSign = $method . "\n" . $bodyHash . "\n" . "\n" . $url;
        $stringToHash = $clientId . $t . $stringToSign;
        $sign = strtoupper(hash_hmac('sha256', $stringToHash, $clientSecret));
        
        $headers = [
            'client_id' => $clientId,
            'sign' => $sign,
            't' => $t,
            'sign_method' => 'HMAC-SHA256',
            'Content-Type' => 'application/json',
        ];
        
        $response = Http::withHeaders($headers)
            ->get('https://openapi.tuyaeu.com/v1.0/token?grant_type=1');
        
        return $response->json();
    }

    public static function getCachedToken()
    {
        $tokenData = Cache::get('tuya_token');
        if ($tokenData && isset($tokenData['expire_time']) && $tokenData['expire_time'] > time()) {
            return $tokenData['access_token'];
        }

        $response = self::getTokenSimple();
        if (isset($response['result']['access_token'])) {
            $expire = $response['result']['expire_time']; // u sekundama
            $tokenData = [
                'access_token' => $response['result']['access_token'],
                'expire_time' => time() + $expire - 60, // 60 sekundi ranije za svaki slučaj
            ];
            Cache::put('tuya_token', $tokenData, $expire - 60);
            return $tokenData['access_token'];
        }

        return null; // ili baci exception
    }

    public static function controlDevice($deviceId, $body)
    {
        $accessToken = self::getCachedToken();
        $clientId = 'jjavkaktjpjv7qycft4d';
        $t = round(microtime(true) * 1000);
        $headers = [
            'client_id' => $clientId,
            'access_token' => $accessToken,
            't' => $t,
            'sign_method' => 'HMAC-SHA256',
            'Content-Type' => 'application/json',
        ];
        $url = "https://openapi.tuyaeu.com/v1.0/devices/{$deviceId}/commands";
        $response = \Illuminate\Support\Facades\Http::withHeaders($headers)
            ->post($url, $body);
        return $response->json();
    }
} 