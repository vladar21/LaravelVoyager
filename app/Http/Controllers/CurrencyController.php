<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use App\Currency;

class CurrencyController extends Controller
{
    public function getcurrency($endpoint)
    {
        
        $fields = \App\Currency::api($endpoint);
        dd($fields);
        //$currency = \App\Currency::add($fields);
        dd($currency);
        //return redirect()->route('rates.index');
    }
}
