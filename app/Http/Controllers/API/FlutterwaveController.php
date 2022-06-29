<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\CssSelector\Node\FunctionNode;

class FlutterwaveController extends Controller
{
  //
  private $finalresult;

  // public function __construct()
  // {
  //   $this->finalresult = [];
  // }

  public static function create_bill_payment($data)
  {
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.flutterwave.com//v3/bills',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $data,


      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . getenv("FLUTTERWAVE_SECRET_KEY"),
        'Content-Type: application/json'
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
      return [
        'status' => false,
        "status_code" => 200,
        "message" => "An Error occured using Flutterwave Bill Payment Api",
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
      "message" => "Flutterwave Bill Payment Api Ran successfully",
      "data" => [
        'errors' => null,
        "values" => json_decode($response),
      ],
      "token" => null,
      "debug" => null
    ];
  }

 

   public function create_bulk_bill_payment($data){
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.flutterwave.com//v3/bulk-bills',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>'{
      "bulk_reference": "edf-12de5223d2f32",
      "callback_url": "https://webhook.site/5f9a659a-11a2-4925-89cf-8a59ea6a019a",
      "bulk_data": [
         {
            "country": "NG",
            "customer": "+23490803840303",
            "amount": 500,
            "recurrence": "WEEKLY",
            "type": "AIRTIME",
            "reference": "930049200929"
          },
          {
            "country": "NG",
            "customer": "+23490803840304",
            "amount": 500,
            "recurrence": "WEEKLY",
            "type": "AIRTIME",
            "reference": "930004912332"
          }
      ]
    }',
    CURLOPT_HTTPHEADER => array(
      'Authorization: Bearer ' . getenv("FLUTTERWAVE_SECRET_KEY"),
      'Content-Type: application/json'
    ),
    ));
    
    $response = curl_exec($curl);
    
    curl_close($curl);
    echo $response;
    
    
   }

   public function get_bill_payment_status($ref){
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.flutterwave.com/v3/bills/".$ref,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer " . getenv("FLUTTERWAVE_SECRET_KEY"),
        "Cache-Control: no-cache",
      ),
    ));
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    $result = "cURL Error #:" . $err;

    $data = json_decode($response);

    if ($data->status == "error") {
      return response_data(false, 422, $data->message, ['errors' => json_decode($result)], false, false);
    } else {
      return response_data(true, 200, $data->message, ['values' => $data->data], false, false);
    }
     
   }

   public function get_bill_categories(){
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.flutterwave.com/v3/bill-categories",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer " . getenv("FLUTTERWAVE_SECRET_KEY"),
        "Cache-Control: no-cache",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    $result = "cURL Error #:" . $err;

    $data = json_decode($response);

    if ($data->status == "error") {
      return response_data(false, 422, $data->message, ['errors' => json_decode($result)], false, false);
    } else {
      return response_data(true, 200, $data->message, ['values' => $data->data], false, false);
    }
  }

  public function get_airtime_bills(){


    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.flutterwave.com/v3/bill-categories",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer " . getenv("FLUTTERWAVE_SECRET_KEY"),
        "Cache-Control: no-cache",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    $result = "cURL Error #:" . $err;

    $data = json_decode($response);

    if ($data->status == "error") {
      return response_data(false, 422, $data->message, ['errors' => json_decode($result)], false, false);
    } else {

     $empty_array = array();
     foreach($data->data as $value){

      if($value->is_airtime == true){
        array_push($empty_array, $value);  
    }
  }

    return response_data(true, 200, 'Airtime bill category fetched', ['values' => $empty_array], false, false);

    }

  }


  public function get_data_bundle_bills(){


    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.flutterwave.com/v3/bill-categories",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer " . getenv("FLUTTERWAVE_SECRET_KEY"),
        "Cache-Control: no-cache",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    $result = "cURL Error #:" . $err;

    $data = json_decode($response);

    if ($data->status == "error") {
      return response_data(false, 422, $data->message, ['errors' => json_decode($result)], false, false);
    } else {

     $empty_array = array();
     foreach($data->data as $value){

      if(preg_match('/data bundle/i', $value->biller_name)){
        array_push($empty_array, $value);  
    }
  }

    return response_data(true, 200, 'Data bundle bill category fetched', ['values' => $empty_array], false, false);

    }

  }



  public function get_electric_bills(){


    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.flutterwave.com/v3/bill-categories",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer " . getenv("FLUTTERWAVE_SECRET_KEY"),
        "Cache-Control: no-cache",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    $result = "cURL Error #:" . $err;

    $data = json_decode($response);

    if ($data->status == "error") {
      return response_data(false, 422, $data->message, ['errors' => json_decode($result)], false, false);
    } else {

     $empty_array = array();
     foreach($data->data as $value){

      if(preg_match('/electric/i', $value->biller_name)){
        array_push($empty_array, $value);  
    }
  }

    return response_data(true, 200, 'Electricity bill category fetched', ['values' => $empty_array], false, false);

    }

  }


  public function get_gotv_bills(){


    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.flutterwave.com/v3/bill-categories",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer " . getenv("FLUTTERWAVE_SECRET_KEY"),
        "Cache-Control: no-cache",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    $result = "cURL Error #:" . $err;

    $data = json_decode($response);

    if ($data->status == "error") {
      return response_data(false, 422, $data->message, ['errors' => json_decode($result)], false, false);
    } else {

     $empty_array = array();
     foreach($data->data as $value){

      if(preg_match('/gotv/i', $value->biller_name)){
        array_push($empty_array, $value);  
    }
  }

    return response_data(true, 200, 'Gotv bill category fetched', ['values' => $empty_array], false, false);

    }

  }


  public function get_dstv_bills(){


    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.flutterwave.com/v3/bill-categories",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer " . getenv("FLUTTERWAVE_SECRET_KEY"),
        "Cache-Control: no-cache",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    $result = "cURL Error #:" . $err;

    $data = json_decode($response);

    if ($data->status == "error") {
      return response_data(false, 422, $data->message, ['errors' => json_decode($result)], false, false);
    } else {

     $empty_array = array();
     foreach($data->data as $value){

      if(preg_match('/dstv/i', $value->biller_name)){
        array_push($empty_array, $value);  
    }
  }

    return response_data(true, 200, 'Dstv bill category fetched', ['values' => $empty_array], false, false);

    }

  }


  public function get_startimes_bills(){


    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.flutterwave.com/v3/bill-categories",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer " . getenv("FLUTTERWAVE_SECRET_KEY"),
        "Cache-Control: no-cache",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    $result = "cURL Error #:" . $err;

    $data = json_decode($response);

    if ($data->status == "error") {
      return response_data(false, 422, $data->message, ['errors' => json_decode($result)], false, false);
    } else {

     $empty_array = array();
     foreach($data->data as $value){

      if(preg_match('/startime/i', $value->biller_name)){
        array_push($empty_array, $value);  
    }
  }

    return response_data(true, 200, 'Startimes bill category fetched', ['values' => $empty_array], false, false);

    }

  }


  public static function get_bill_payment_by_ref_id($ref)
  {
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.flutterwave.com/v3/bills/" . $ref,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer " . getenv("FLUTTERWAVE_SECRET_KEY"),
        "Content-Type: application/json"
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
      return response_data(false, 422, "Sorry a Validation Error Occured", ['errors' => json_decode($result)], false, false);
    } else {
      return response_data(true, 200, 'Account Successfully Updated.', ['values' => $response], false, false);
    }
  }


  public static function get_bill_payments($from_date, $to_date, $page, $ref)
  {
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.flutterwave.com//v3/bills?from=' . $from_date . '&to=' . $to_date . '&page=' . $page . '&reference=' . $ref,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . getenv("FLUTTERWAVE_SECRET_KEY"),
        'Content-Type: application/json'
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
      return response_data(false, 422, "Sorry a Validation Error Occured", ['errors' => json_decode($result)], false, false);
    } else {
      return response_data(true, 200, 'Account Successfully Updated.', ['values' => $response], false, false);
    }

  }



  ########################################################################
  ################# Flutterwave virtual card SDKs files ##################
  ########################################################################

  public static function create_virtual_card(){
    $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.flutterwave.com/v3/virtual-cards/transfers/166948/retries",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "Authorization: Bearer ". getenv("FLUTTERWAVE_SECRET_KEY"),
    "Content-Type: application/json"
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
  }


  public static function get_all_virtual_card(){
    $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.flutterwave.com/v3/virtual-cards",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "Content-Type: application/json",
    "Authorization: Bearer ". getenv("FLUTTERWAVE_SECRET_KEY")
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
  }


  public static function get_a_virtual_card($id){
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.flutterwave.com/v3/virtual-cards/".$id,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "Content-Type: application/json",
        "Authorization: Bearer ". getenv("FLUTTERWAVE_SECRET_KEY")
      ),
    ));
    
    $response = curl_exec($curl);
    
    curl_close($curl);
    echo $response;
  }


  public static function fund_a_virtual_card($id, $data=array()){
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.flutterwave.com/v3/virtual-cards/".$id."/fund",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => $data,
    CURLOPT_HTTPHEADER => array(
      "Content-Type: application/json",
      "Authorization: Bearer ". getenv("FLUTTERWAVE_SECRET_KEY")
    ),
  ));
  
  $response = curl_exec($curl);
  curl_close($curl);
  echo $response;

}


public static function witdraw_from_a_virtual_card($id, $data=array()){
  $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.flutterwave.com/v3/virtual-cards/".$id."/withdraw",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => $data,
  CURLOPT_HTTPHEADER => array(
    "Content-Type: application/json",
    "Authorization: Bearer ". getenv("FLUTTERWAVE_SECRET_KEY")
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
}



public static function block_and_unblock_virtual_card($id, $action){
  $curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.flutterwave.com/v3/virtual-cards/".$id."/status/".$action,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "PUT",
  CURLOPT_HTTPHEADER => array(
    "Content-Type: application/json",
    "Authorization: Bearer ". getenv("FLUTTERWAVE_SECRET_KEY")
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
}



public static function terminate_a_virtual_card($id){
  $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.flutterwave.com/v3/virtual-cards/".$id."/terminate",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "PUT",
  CURLOPT_HTTPHEADER => array(
    "Content-Type: application/json",
    "Authorization: Bearer ". getenv("FLUTTERWAVE_SECRET_KEY")
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
}

public static function get_virtual_card_transactions($id){
  $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.flutterwave.com/v3/virtual-cards/".$id."/transactions",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "Content-Type: application/json",
    "Authorization: Bearer ". getenv("FLUTTERWAVE_SECRET_KEY")
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;

}




   }


   
    



