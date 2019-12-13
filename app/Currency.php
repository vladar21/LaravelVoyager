<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Rate;
use Carbon\Carbon;

class Currency extends Model
{
    public $timestamps = false; // Чтобы Laravel Eloquent автоматически не добавляла данные в поля created_at и updated_at

    public function rate()
    {
        return $this->hasMany('App\Rate');
    }

    public static function add($fields)
    {
        $rate = new static;
        $rate->fill($fields);
        $rate->save();
    }

    public static function indexNbu($currentDate)
    {
        $currentDate = Carbon::create($currentDate);        
        $currentDate->toDateString();
        $currentDate = $currentDate->format('Ymd');  
        
        $ch = curl_init('https://old.bank.gov.ua/NBUStatService/v1/statdirectory/exchange?date='.$currentDate.'&json');
    
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Store the data:
        $json = curl_exec($ch);
    
        curl_close($ch);

        $exchangeRates = json_decode($json, true);
       
        foreach($exchangeRates as $exch)
        {            
            $currency = new Currency;
            $currency->namecurrency = $exch['txt'];
            $currency->save();
        } 
       
    }

    public static function api($RequestUri)
    {
       
        // set API Endpoint and API key 
        if (count(explode("/",$RequestUri))>1)
        {
            $endpoint = explode("?", explode("/",$RequestUri)[2])[0];
            $url = explode("?", explode("/",$RequestUri)[2])[1];
            $arrayurl = explode("&", $url);
            $urlapi = "";
            $symbols = "&symbols=";
            foreach($arrayurl as $a)
            {
                if (strpos($a, "symbols") !== false) 
                {
                    $symbols = $symbols.explode("=", $a)[1].",";
                    
                    continue;
                }
                
                $urlapi = $urlapi."&".$a;
            }
            $symbols = mb_substr($symbols, 0, -1);
            $urlapi = $urlapi.$symbols;
        }
        else
        {
            $endpoint = $RequestUri;
            $urlapi = "";
        }
        
        //dd($urlapi);
        
        $access_key = '8e996550c4c1e3a717f4bb88f4173fec';
        
        dd('http://data.fixer.io/api/'.$endpoint.'?access_key='.$access_key.$urlapi);
        // Initialize CURL:
        $ch = curl_init('http://data.fixer.io/api/'.$endpoint.'?access_key='.$access_key.'&'.$urlapi);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Store the data:
        $json = curl_exec($ch);
        curl_close($ch);
      
        // Decode JSON response:
        $exchangeRates = json_decode($json, true);
        
        // Access the exchange rate values, e.g. GBP:
        //echo $exchangeRates['rates']['GBP'];
        //$namecurrency =  array_keys($exchangeRates['rates']);
        //$namecurrency->all();

        
        return $exchangeRates;
    }
}
