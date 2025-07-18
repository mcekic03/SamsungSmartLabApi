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
        
        $json = $response->json();
        $json['sign'] = $sign;
        return $json;
    }


    public static function controlDevice($deviceId, $body)
    {
        $resp = \App\Tuya::getTokenSimple();
        $accessToken = $resp['result']['access_token'];
        $sign = $resp['sign'];
        $t = $resp['t'];
        $clientId = 'jjavkaktjpjv7qycft4d';
       $headers = [
            'sign_method'   => 'HMAC-SHA256',
            'client_id'     => $clientId,
            't'             => $t,
            'mode'          => 'cors',
            'Content-Type'  => 'application/json',
            'sign'          => $sign,         // generisan sign string
            'access_token'  => $accessToken,  // tvoj access token
        ];
        $url = "https://openapi.tuyaeu.com/v1.0/devices/{$deviceId}/commands";
        $response = Http::withHeaders($headers)->post($url, $body);
        return $response->json();
    }



} 