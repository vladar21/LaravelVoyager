<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Rate;
use Carbon\Carbon;

class Currency extends Model
{
    public $timestamps = false; // Чтобы Laravel Eloquent автоматически не добавляла данные в поля created_at и updated_at

    public function rate()
    {
        return $this->hasMany('App\Rate');
    }

    public static function charttable()
    {
        $temp = DB::table('rates')
            ->select('date', 'currencies.codecurrency as code', 'value')
            ->leftJoin('currencies', 'currencies.id', '=', 'currency_id')
            ->where('currencies.flagwork', '=', 1)
            ->orderby('date')            
            ->get();

        $temp = $temp->groupBy(function($item) {
            return $item->date;
        });
        
        $codes = Currency::all()->where('flagwork', 1);
                        
        $string = 'Date';
        foreach($codes as $code){           
           $string .= ", ".$code->codecurrency;            
        }
        
        $result[] = [$string];
        //dd(json_encode($result));

        foreach($temp as $t){    
            $string[] = [Carbon::create($t[0]->date)->format('d.m.y')];                    
            foreach($t as $t1){
                array_push($string, $t1->value);
            }
            array_push($result, $string);
        }
        dd($result);

        return json_encode($result);
    }
    // public static function add($fields)
    // {
    //     $rate = new static;
    //     $rate->fill($fields);
    //     $rate->save();
    // }
    public static function flagbasereset()
    {
        $prev = \App\Currency::where('flagbase', 1)->get()->first();
        //dd($prev);
        if ($prev && $prev->count()) 
        {
            $prev->flagbase = 0;
            $prev->save();
        }
    }
    public static function flagbaseinstall($id)
    {
        $basecurrency = \App\Currency::find($id);
        $basecurrency->flagbase = 1;
        $basecurrency->save();
    }
    public static function flagworkreset()
    {
        $workcurrencies = \App\Currency::where('flagwork', 1)->get();
        //dd($workcurrencies);
        foreach($workcurrencies as $workcurrency)
        {            
            $workcurrency->flagwork = 0;
            $workcurrency->save();
        }        
    }
    public static function flagworkinstall($id)
    {
        $workcurrency = \App\Currency::find($id);
        $workcurrency->flagwork = 1;
        $workcurrency->save();
    }
    public static function indexNbu($currentDate)
    {
        $currentDate = Carbon::create($currentDate);        
        $currentDate->toDateString();
        $currentDate = $currentDate->format('Ymd');  
        
        //dd($currentDate);
        $ch = curl_init('https://old.bank.gov.ua/NBUStatService/v1/statdirectory/exchange?date='.$currentDate.'&json');

        //dd($ch);
    
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Store the data:
        $json = curl_exec($ch);
        //dd($json);
        curl_close($ch);

        $exchangeRates = json_decode($json, true);
       //dd($exchangeRates);
        foreach($exchangeRates as $exch)
        {            
            $currency = new Currency;
            $currency->namecurrency = $exch['txt'];
            $currency->codecurrency = $exch['cc'];
            $currency->save();
        }
        $currency = new Currency;
        $currency->namecurrency = 'Українська гривня';
        $currency->codecurrency = 'UAH';
        $currency->flagbase = 1;
        $currency->save();
       
    }

    // public static function api($RequestUri)
    // {
       
    //     // set API Endpoint and API key 
    //     if (count(explode("/",$RequestUri))>1)
    //     {
    //         $endpoint = explode("?", explode("/",$RequestUri)[2])[0];
    //         $url = explode("?", explode("/",$RequestUri)[2])[1];
    //         $arrayurl = explode("&", $url);
    //         $urlapi = "";
    //         $symbols = "&symbols=";
    //         foreach($arrayurl as $a)
    //         {
    //             if (strpos($a, "symbols") !== false) 
    //             {
    //                 $symbols = $symbols.explode("=", $a)[1].",";
                    
    //                 continue;
    //             }
                
    //             $urlapi = $urlapi."&".$a;
    //         }
    //         $symbols = mb_substr($symbols, 0, -1);
    //         $urlapi = $urlapi.$symbols;
    //     }
    //     else
    //     {
    //         $endpoint = $RequestUri;
    //         $urlapi = "";
    //     }
        
    //     //dd($urlapi);
        
    //     $access_key = '8e996550c4c1e3a717f4bb88f4173fec';
        
    //     dd('http://data.fixer.io/api/'.$endpoint.'?access_key='.$access_key.$urlapi);
    //     // Initialize CURL:
    //     $ch = curl_init('http://data.fixer.io/api/'.$endpoint.'?access_key='.$access_key.'&'.$urlapi);
        
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //     // Store the data:
    //     $json = curl_exec($ch);
    //     curl_close($ch);
      
    //     // Decode JSON response:
    //     $exchangeRates = json_decode($json, true);
        
    //     // Access the exchange rate values, e.g. GBP:
    //     //echo $exchangeRates['rates']['GBP'];
    //     //$namecurrency =  array_keys($exchangeRates['rates']);
    //     //$namecurrency->all();

        
    //     return $exchangeRates;
    // }
}
