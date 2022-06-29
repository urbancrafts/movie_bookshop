<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    //

    public static function SendPlainSMS($to, $message)
    {
        $url = 'https://termii.com/api/sms/send';
        $data = [
            "to" => _format_number($to),
            "from" => "PAYBUYMAX",
            "sms" => $message,
            "type" => "plain",
            "channel" => "generic",
            "api_key" => getenv("TERMII_API_KEY")
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return [
                'status' => false,
                "status_code" => 422,
                "message" => "An Error occured Sending Sms to Customer",
                "data" => [
                    'errors' => json_decode($err),
                    "values" => null,
                ],
                "token" => null,
                "debug" => null
            ];
        }

        return [
            'status' => true,
            "status_code" => 200,
            "message" => "SMS sent successfully.",
            "data" => [
                'errors' => null,
                "values" => json_decode($response),
            ],
            "token" => null,
            "debug" => null
        ];
    }

    public static function sendOneTimeToken($to)
    {
        $url = "https://api.ng.termii.com/api/sms/otp/send";

        $curl = curl_init();
        $data = array(
            "api_key" => getenv("TERMII_API_KEY"),
            "message_type" => "NUMERIC",
            "to" => _format_number($to),
            "from" => "PAYBUYMAX",
            "channel" => "dnd",
            "pin_attempts" => 10,
            "pin_time_to_live" =>  getenv("CODE_EXPIRY_TIME"),
            "pin_length" => getenv("PIN_LENGTH"),
            "pin_placeholder" => "< 123456 >",
            "message_text" => "Your pin is" . rand(111111, 999999),
            "pin_type" => "NUMERIC"
        );

        $post_data = json_encode($data);

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $post_data,
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return [
                'status' => false,
                "status_code" => 422,
                "message" => "An Error occured Sending OTP via SMS",
                "data" => [
                    'errors' => json_decode($err),
                    "values" => null,
                ],
                "token" => null,
                "debug" => null
            ];
        }

        return [
            'status' => true,
            "status_code" => 200,
            "message" => "OTP sent via SMS successfully.",
            "data" => [
                'errors' => null,
                "values" => json_decode($response),
            ],
            "token" => null,
            "debug" => null
        ];
    }

    public static function sendBulkSMS(array $to_array, $message)
    {
        $url = "https://api.ng.termii.com/api/sms/send/bulk";
        $curl = curl_init();

        foreach($to_array as $number){
            $number = _format_number($number);
        }

        $data = array(
            "to" => $to_array, "from" => getenv("APP_NAME"),
            "sms" => $message, "type" => "plain", "channel" => "generic", "api_key" => getenv("TERMII_API_KEY")
        );

        $post_data = json_encode($data);

        curl_setopt_array($curl, array(
            CURLOPT_URL => '',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $post_data,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        if ($err) {
            return [
                'status' => false,
                "status_code" => 422,
                "message" => "An Error occured Sending bulk SMS",
                "data" => [
                    'errors' => json_decode($err),
                    "values" => null,
                ],
                "token" => null,
                "debug" => null
            ];
        }

        return [
            'status' => true,
            "status_code" => 200,
            "message" => "Bulk SMS sent successfully.",
            "data" => [
                'errors' => null,
                "values" => json_decode($response),
            ],
            "token" => null,
            "debug" => null
        ];
    }
}
