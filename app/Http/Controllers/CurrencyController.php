<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use App\Currency;

class CurrencyController extends Controller
{
    public function index()
    {
        $exchangeRates = \App\Currency::api('latest');
        $names = $exchangeRates['rates']->rates;
        dd($names);
        return view('currency', ['names' => $names]);
    }

    public function getcurrency(Request $request)
    {
        
        $fields = \App\Currency::api($request);
        dd($fields);
        //$currency = \App\Currency::add($fields);
        dd($currency);
        //return redirect()->route('rates.index');
    }
}
