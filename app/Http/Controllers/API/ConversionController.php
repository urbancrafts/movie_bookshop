<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConversionController extends Controller
{
    // fetches the currency rates for the last_24_hours
    public static function get_last_twenty_four_hours_rate($currency_pair)
    {
        $url = "/ticker/" . $currency_pair;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => getenv('BITSTAMP_URL') . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if($err){
            return response_data(false, 422, "Sorry an Error occured fetching the conversion rate for the last 24 hours", ['errors' => json_decode($err)], false, false);
        }

        return response_data(true, 200, "Conversion Rate for the last 24 hours fetched successfully", ['values' => json_decode($response)], false, false);
    }

    // fetch the currency rate for the last 1 hour 
    public static function get_last_one_hour_rates($currency_pair){
        $url = "/ticker_hour/" . $currency_pair;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => getenv('BITSTAMP_URL') . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if($err){
            return response_data(false, 422, "Sorry an Error occured fetching the conversion rate for the last 1 hour", ['errors' => json_decode($err)], false, false);
        }

        return response_data(true, 200, "Conversion Rate for the last 1 hour fetched successfully", ['values' => json_decode($response)], false, false);
    }   

    // fetch all currency pairs 
    public function fetch_all_currency_pairs(){
        return response_data(true, 200, "All currency pairs fetched successfully", ['values' => _all_currency_pairs()], false, false);
    }

    // fetch one currency pair 
    public function fetch_one_currency_pair($currency_pair){
        $all_currency_pairs = _all_currency_pairs();

        foreach($all_currency_pairs as $item){
            if($item == $currency_pair){
                return response_data(true, 200, "Currency pair found and fetched successfully", ['values' => $item], false, false);
            }else{
                return response_data(false, 422, "Currency pair not found", false, false, false);
            }
        }
    }

    // fetch real time trading pair 

    public static function get_realtime_trading_pairs()
    {
        $url = "/trading-pairs-info/";

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => getenv('BITSTAMP_URL') . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if($err){
            return response_data(false, 422, "Sorry an Error occured fetching the trading pairs", ['errors' => json_decode($err)], false, false);
        }

        return response_data(true, 200, "All trading pairs fetched successfully", ['values' => json_decode($response)], false, false);
    }

    public static function get_last_minute_rates($currency_pair)
    {
        // doc here https://www.bitstamp.net/api/#ohlc_data
        // step is for the time frame Timeframe in seconds. 
        // Possible options are 60, 180, 300, 900, 1800, 3600, 7200, 14400, 21600, 43200, 86400, 259200
        
        // limit is for the number of records that should be fetched 
        // Limit OHLC results (minimum: 1; maximum: 1000)
        
        // for this endpoint we will be using 60 sec and 100 record limit 
        $url = "/ohlc/" . $currency_pair . "?step=60&limit=100";

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => getenv('BITSTAMP_URL') . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if($err){
            return response_data(false, 422, "Sorry an Error occured fetching the conversion rate for the last 60 seconds", ['errors' => json_decode($err)], false, false);
        }

        return response_data(true, 200, "Conversion Rate for the last 60 seconds fetched successfully", ['values' => json_decode($response)], false, false);
    }

    // fetch rates with custom time frame limits and records 
    public static function fetch_rates_with_options($currency_pair,$timeframe,$limit)
    {
        // doc here https://www.bitstamp.net/api/#ohlc_data
        // step is for the time frame Timeframe in seconds. 
        // Possible options are 60, 180, 300, 900, 1800, 3600, 7200, 14400, 21600, 43200, 86400, 259200
        
        // limit is for the number of records that should be fetched 
        // Limit OHLC results (minimum: 1; maximum: 1000)
        
        // for this endpoint we will have a custom time in minutes and data limit 

        $convert_time_to_sec = _convert_minutes_to_seconds($timeframe);
    
        $url = "/ohlc/" . $currency_pair . "?step=". $convert_time_to_sec ."&limit=". $limit;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => getenv('BITSTAMP_URL') . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if($err){
            return response_data(false, 422, "Sorry an Error occured fetching the conversion rate for the last 60 seconds", ['errors' => json_decode($err)], false, false);
        }

        return response_data(true, 200, "Conversion Rate for the last 60 seconds fetched successfully", ['values' => json_decode($response)], false, false);
    }
    

}
