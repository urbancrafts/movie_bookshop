<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PaystackCustomerController extends Controller
{
    /**************************************************
    public static function verify_customer(Request $request){

        //valid credential
        $validator = Validator::make($request->all(), [
            'account_number' => 'required|min:10',
            'bank_code' => 'required'
        ]);


         //check if there's validation error 
        if ($validator->fails()) {
          return response_data(false, 422, "Sorry a Validation Error Occured", ['errors' => $validator->errors()->all()], false, false);
        }

        $account_number = $request->account_number;
        $bank_code = $request->bank_code;

    }
}
**********************************************************/

    public static function verify_customer($account_number, $bank_code){

        //valid credential
        // $validator = Validator::make($credentials, [
        //     'account_number' => 'required|string',
        //     'bank_code' => 'required|integer'
        // ]);


        //  //check if there's validation error 
        // if ($validator->fails()) {
        //     return response()->json(['error' => $validator->messages()], 200);
        // }



        $curl = curl_init();
  
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.paystack.co/bank/resolve?account_number=".$account_number."&bank_code=".$bank_code,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer " . getenv("PAYSTACK_API_SEC_KEY"),
            "Cache-Control: no-cache",
          ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
         
        $result = "cURL Error #:" . $err;

        if ($err) {
            return response_data(false, 422, "Sorry an Error Ocurred", ['errors' => json_decode($result)], false, false);
        } else {
          return response_data(true, 200, 'Account Successfully Updated.', ['values' => json_decode($response)], false, false);
        }
    }
}
