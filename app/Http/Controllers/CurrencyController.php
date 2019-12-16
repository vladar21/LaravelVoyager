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
        
        $rates = Rate::all('date');    
        
        $dates = ($rates && $rates->count())?(Rate::all('date')->pluck('date')[0]):($currentDate);      
        if ($dates != $currentDate)
        {
        $nothing = false;               
        }
      
        if ($nothing) 
        {
            \App\Currency::truncate();
            \App\Rate::truncate();
            \App\Currency::indexNbu($currentDate);
            \App\Rate::indexNbu($currentDate);
        }
       
        $currencies = Currency::all()->sortBy('namecurrency');//all('namecurrency')->pluck('namecurrency');
       
        return view('currency')->with('currencies', $currencies);
    }

    public function getnbu(Request $request)
    {
        $parametrs = $request->query('base');

        // устанавливаем базовую валюту

        \App\Currency::flagbasereset();
       
        $prev = \App\Currency::where('codecurrency', $parametrs)->get()->first();

        if ($prev && $prev->count()) 
        {
           $id = $prev->id;
        }
        
        \App\Currency::flagbaseinstall($id);
        
        // Сбрасываем предыдущие рабочие валюты
        \App\Currency::flagworkreset();
        // устанавливаем рабочие валюты   
        $parametrs = $request->query('symbols');

        foreach($parametrs as $parametr)
        {
            
            $prev = \App\Currency::where('codecurrency', $parametr)->get()->first();
            \App\Currency::flagworkinstall($prev->id);
        }
        $result = \App\Currency::charttable();

        return view('chart')->with('result', $result);
    }

}
