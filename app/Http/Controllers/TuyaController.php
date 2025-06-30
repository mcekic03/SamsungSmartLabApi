<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Tuya;

class TuyaController extends Controller
{

// Alternativno, još jednostavnija verzija koja sigurno radi:
public function getTokenSimple()
{
    return Tuya::getTokenSimple();
}

    
}
