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
    // готовим данные для графиков
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
                        
        $str = ['Date'];       
        foreach($codes as $code){           
           array_push($str, $code->codecurrency);            
        }
        $result[] = $str;

        foreach($temp as $t){  
            $stroka = [Carbon::create($t[0]->date)];                 
            foreach($t as $t1){
                array_push($stroka, (float)$t1->value);
            }

            array_push($result, $stroka);
            $stroka = [];
        }
       
       
        return json_encode($result);
    }
    
    public static function flagbaseinstall($id)
    {
        $prev = \App\Currency::where('flagbase', 1)->get()->first();
        if ($prev && $prev->count()) 
        {
            $prev->flagbase = 0;
            $prev->save();
        }

        $basecurrency = \App\Currency::find($id);
        $basecurrency->flagbase = 1;
        $basecurrency->save();
        
        // далее должен идти пересчет курсов по базовой валюте, но без количества единиц валюты, по которому НБУ делал расчет - не имеет смысла. API НБУ эту цифру не дает.
        
        // $rates = \App\Rate::all();
        // foreach($rates as $rate){

        // }

    }
    public static function flagworkreset()
    {
        $workcurrencies = \App\Currency::where('flagwork', 1)->get();

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
            $currency->codecurrency = $exch['cc'];
            $currency->save();
        }
        $currency = new Currency;
        $currency->namecurrency = 'Українська гривня';
        $currency->codecurrency = 'UAH';
        $currency->flagbase = 1;
        $currency->save();       
    }

}
