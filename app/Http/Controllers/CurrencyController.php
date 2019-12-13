<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Currency;
use App\Rate;

class CurrencyController extends Controller
{
    public function index()
    {
        $nothing = true;
        $currentDate = Carbon::now()->format('d.m.Y');
        
        $dates = Rate::all('date')->pluck('date'); 
        
        foreach($dates as $date)
        {
             if ($date == $currentDate)
             {
                $nothing = false;
                break;
             }
        }
        if ($nothing) 
        {
            \App\Currency::indexNbu($currentDate);
            \App\Rate::indexNbu($currentDate);
        }
        // \App\Currency::indexNbu($currentDate);

        //$currencies = Currency::all()->getatri;
        $currencies = Currency::all('namecurrency')->pluck('namecurrency');
        
        return view('currency')->with('currencies', $currencies);
    }

    public function getnbu(Request $request)
    {
        
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
