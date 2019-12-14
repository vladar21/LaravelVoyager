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
        //dd($request);
        $parametrs = $request->query('base');
        //dd($parametrs);

        // устанавливаем базовую валюту

        \App\Currency::flagbasereset();
       
        $prev = \App\Currency::where('codecurrency', $parametrs)->get()->first();
        //dd($prev->id);
        if ($prev && $prev->count()) 
        {
           $id = $prev->id;
        }
        
        //dd($id);
        \App\Currency::flagbaseinstall($id);
        
        // Сбрасываем предыдущие рабочие валюты
        \App\Currency::flagworkreset();
        // устанавливаем рабочие валюты   
        //$parametrs = $request->get(['symbols']);//->groupBy('symbols')->keys()->all();
        $parametrs = $request->query('symbols');

        //dd($parametrs);
        foreach($parametrs as $parametr)
        {
            
            $prev = \App\Currency::where('codecurrency', $parametr)->get()->first();
            //dd($prev->id);
            \App\Currency::flagworkinstall($prev->id);
        }
        \App\Currency::charttable();
        return view('chart');//->with('currencies', $currencies);
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
