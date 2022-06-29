<?php

use App\Models\Code;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

define('SECOND_TO_MINUTE',60);

if (!function_exists('code_expire_check')) {
    function code_expire_check($code)
    {
        $code_details = code_details($code);
        $expiry_date = strtotime($code_details[0]->expiry_date);
        $today = strtotime(date('Y-m-d h:i:sa'));
        $difference = round(($today - $expiry_date) / 60, 2);
        return $difference > round(0, 2);
    }
}


if (!function_exists('code_exists_check')) {
    function code_exists_check($code)
    {
        $code_count = DB::table('codes')->where('value', $code)->where('status', 1)->count();
        return intval($code_count) > 0 ? true : false;
    }
}

if (!function_exists('code_exists_email_check')) {
    function code_exists_email_check($email, $code)
    {
        // check if the code is for the email 
        $code_count = DB::table('codes')->where('value', $code)->where('email', $email)->where('status', 1)->count();
        return intval($code_count) > 0 ? true : false;
    }
}



if (!function_exists('code_details')) {
    function code_details($code)
    {
        if (code_exists_check($code)) {
            return DB::table('codes')->where('value', '=', $code)->where('status', '=', 1)->get();
        } else {
            return false;
        }
    }
}


if (!function_exists('generate_token')) {
    function generate_token($email)
    {
        $user = DB::table('users')->where('email', $email);

        if ($user->count() > 0) {
            // user doesnt exist with that email address 
            return $user->createToken('Laravel Password Grant Client')->accessToken;
        } else {
            return false;
        }
    }
}

if (!function_exists('delete_code')) {
    function delete_code($code)
    {
        $code = DB::table('codes')->where('value', $code);

        if ($code->count() > 0) {
            // code doesnt exist with that email address 
            return $code->delete() ? true : false;
        } else {
            return false;
        }
    }
}


if (!function_exists('generate_code')) {
    function generate_code($email, $type)
    {
        // generate the code 
        $code = Str::random(6);

        $time = date('Y-m-d H:i');

        $newtimestamp = strtotime($time . '+' . env('CODE_EXPIRY_TIME') . 'minute');

        $convert_datetime = date('Y-m-d H:i', $newtimestamp);

        $save_code = Code::create([
            'value' => $code,
            'email' => $email,
            'type' => $type,
            'duration' => env('CODE_EXPIRY_TIME'),
            'expiry_date' => $convert_datetime,
        ]);


        if ($save_code) {
            // code created successfully 
            return $save_code;
        } else {
            return false;
        }
    }
}


if (!function_exists('response_data')) {
    function response_data($status = false, $status_code = false, $message = false, $data = false, $token = false, $debug = false)
    {
        return response()->json(array(
            'status' => $status ? $status : false,
            'status_code' => $status_code ? $status_code : 200,
            'message' => $message ? $message : null,
            'data' =>  $data ? (object) [
                'errors' => isset($data['errors']) ? $data['errors'] : null,
                'values' => isset($data['values']) ? $data['values'] : null,
            ] : [],
            'token' => $token ? $token : null,
            'debug' => $debug ? $debug : null
        ), $status_code ? $status_code : 200);
    }
}

if (!function_exists('get_ip_address')) {
    function get_ip_address()
    {
        return trim(@file_get_contents("https://api.ipify.org/"));
    }
}

if (!function_exists('_ip')) {
    function _ip()
    {
        $ip = 'UNKNOWN';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
}

if (!function_exists('_format_number')) {
    function _format_number($number)
    {
        $a = str_replace(" ", "", $number);
        $b = str_replace("+234", "0", $a);
        $c = str_replace(",", "", $b);

        $d = str_replace("+", "", $c);
        $e = str_replace("*", "", $d);
        $f = str_replace("#", "", $e);
        return '234' . substr($f, 1);
    }
}

if (!function_exists('_2fa_check')) {
    function _2fa_check($email)
    {
        $user_details =  DB::table('users')->where('email', $email)->get();
        if (count($user_details) > 0) {
            // return $user_details[0]->2fa_status;
        } else {
            return false;
        }
    }
}

if (!function_exists('_sms_status_check')) {
    function _sms_status_check($email)
    {
        $user_details =  DB::table('users')->where('email', $email)->get();
        return count($user_details) > 0 ? $user_details['sms_status'] : false;
    }
}

if (!function_exists('_all_currency_pairs')) {
    function _all_currency_pairs()
    {
        $all_pairs = "btcusd, btceur, btcgbp, btcpax, gbpusd, gbpeur, eurusd, xrpusd, xrpeur, xrpbtc, xrpgbp, xrppax, ltcbtc, ltcusd, ltceur, ltcgbp, ethbtc, ethusd, etheur, ethgbp, ethpax, bchusd, bcheur, bchbtc, bchgbp, paxusd, paxeur, paxgbp, xlmbtc, xlmusd, xlmeur, xlmgbp, linkusd, linkeur, linkgbp, linkbtc, linketh, omgusd, omgeur, omggbp, omgbtc, usdcusd, usdceur, btcusdc, ethusdc, eth2eth, aaveusd, aaveeur, aavebtc, batusd, bateur, batbtc, umausd, umaeur, umabtc, daiusd, kncusd, knceur, kncbtc, mkrusd, mkreur, mkrbtc, zrxusd, zrxeur, zrxbtc, gusdusd, algousd, algoeur, algobtc, audiousd, audioeur, audiobtc, crvusd, crveur, crvbtc, snxusd, snxeur, snxbtc, uniusd, unieur, unibtc, yfiusd, yfieur, yfibtc, compusd, compeur, compbtc, grtusd, grteur, usdtusd, usdteur, usdcusdt, btcusdt, ethusdt, xrpusdt, eurteur, eurtusd, maticusd, maticeur, sushiusd, sushieur, chzusd, chzeur, enjusd, enjeur, hbarusd, hbareur, alphausd, alphaeur, axsusd, axseur, fttusd, ftteur, sandusd, sandeur, storjusd, storjeur, adausd, adaeur, adabtc, fetusd, feteur, rgtusd, rgteur, sklusd, skleur, celusd, celeur, slpusd, slpeur, sxpusd, sxpeur, sgbusd, sgbeur, avaxusd, avaxeur, dydxusd, dydxeur, ftmusd, ftmeur, ampusd, ampeur, galausd, galaeur, perpusd, perpeur, wbtcbtc, ctsiusd, ctsieur, cvxusd, cvxeur, imxusd, imxeur, nexousd, nexoeur, ustusd, usteur, antusd, anteur, godsusd, godseur, radusd, radeur";

        return explode(', ', $all_pairs);
    }
}

if (!function_exists('_convert_second_to_minutes')) {
    function _convert_second_to_minutes($second)
    {
        $minute = $second / SECOND_TO_MINUTE;
        // round down 
        return round($minute,2);
    }
}

if (!function_exists('_convert_minutes_to_seconds')) {
    function _convert_minutes_to_seconds($minute)
    {
        $seconds = $minute * SECOND_TO_MINUTE;
        // round  
        return round($seconds);
    }
}