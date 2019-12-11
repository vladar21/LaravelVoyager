<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
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

    public static function api($request)
    {
        //dd($request);
        // set API Endpoint and API key 
        //$endpoint = 'latest';
        $access_key = '8e996550c4c1e3a717f4bb88f4173fec';

        // Initialize CURL:
        $ch = curl_init('http://data.fixer.io/api/'.$request.'?access_key='.$access_key.'');
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
