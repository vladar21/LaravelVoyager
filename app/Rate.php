<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Rate extends Model
{
    public $timestamps = false; // Чтобы Laravel Eloquent автоматически не добавляла данные в поля created_at и updated_at

    public function currency()
    {        
        return $this->hasOne('App\Currency');
    }

    public static function indexNbu($currentDate)
    {
        $startDate = Carbon::create($currentDate); 
           
        $finishDate =  Carbon::create($currentDate);
        $finishDate = $finishDate->subDays(14);
        $weekenddays = $finishDate->diffInWeekendDays($startDate);
        
        $finishDate = $finishDate->subDays($weekenddays);
        
        do{
            if ($startDate->format('w') != 6 || $startDate->format('w') != 0)
            {
                $ch = curl_init('https://old.bank.gov.ua/NBUStatService/v1/statdirectory/exchange?date='.$startDate->format('Ymd').'&json');
                
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $json = curl_exec($ch);

                curl_close($ch);
            
                $exchangeRates = json_decode($json, true);
                
                foreach($exchangeRates as $exch)
                { 
                    $rate = new Rate;                
                    $rate->currency_id = Currency::where('namecurrency', $exch['txt'])->first()->id;                           
                    $rate->value = $exch['rate'];
                    $rate->date = Carbon::create($exch['exchangedate']);                 
                    $rate->save();
                } 
                // вводим данные по украинской гривне
                $rate = new Rate;                
                $rate->currency_id = Currency::where('codecurrency', 'UAH')->first()->id;
                $rate->value = 100;
                $rate->date = $startDate;                 
                $rate->save();
            }               
             $startDate = $startDate->subDay();      
             
         }
         while($startDate > $finishDate);
       
    }
}
