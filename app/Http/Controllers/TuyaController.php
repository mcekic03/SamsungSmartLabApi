<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TuyaController extends Controller
{

// Alternativno, još jednostavnija verzija koja sigurno radi:
public function getTokenSimple()
{
    $clientId = 'jjavkaktjpjv7qycft4d';
    $clientSecret = '638a4e68c7db4f019f495358aea3453b';
    $t = round(microtime(true) * 1000);
    
    // Za osnovni token zahtev
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

    
}
