<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use App\Currency;

class CurrencyController extends Controller
{
    public function index()
    {
        $exchangeRates = \App\Currency::api('latest');
       
        return view('currency', ['exchangeRates' => $exchangeRates]);
    }

    public function getcurrency(Request $request)
    {
        $RequestUri = $request->getRequestUri();
        $fields = \App\Currency::api($RequestUri);
        dd($fields);
        //$currency = \App\Currency::add($fields);
        dd($currency);
        //return redirect()->route('rates.index');
    }
}
