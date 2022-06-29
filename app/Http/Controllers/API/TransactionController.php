<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BankAccountDetail;
use App\Models\Transaction;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\PaystackCustomerController;
use App\Http\Controllers\API\FlutterwaveController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyCodeMail;
use App\Mail\VerifyCodeTransactionMail;
use App\Models\Code;
//use Tymon\JWTAuth\Facades\JWTAuth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class TransactionController extends Controller
{

    protected $user;
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function index()
    {
    }

    public function create_bill_payment(Request $request)
    {
        $credentials = $request->only('token', 'amount', 'biller_name', 'country', 'customer', 'package_data', 'recurrence', 'type');

        //valid credential
        $validator = Validator::make($credentials, [
            'token' => 'required',
            'amount' => 'required|integer', //Anount of pill to be paid for
            'biller_name' => 'required', //The service vendor to be subscribed to
            'country' => 'required', //Country param in short form(Example: NG)
            'customer' =>  'required', //Customer data(mobile)
            'package_data' => 'required', //The type of service to be subscribed to(Example: Data/Airtime)
            'recurrence' => 'required', //Recurrent mode
            //'reference' => 'required',//Transaction reference code
            'type' =>  'required'
        ]);
        //check if there's validation error 
        if ($validator->fails()) {
            return response_data(false, 422, "Sorry a Validation Error Occured", ['errors' => $validator->errors()->all()], false, false);
        }

        // $token = JWTAuth::authenticate($request->token);
        // //query user table with session id
        // $user_check = User::where('id', $token->id)->get();

        // if (count($user_check) > 0) {
            //wrapp form fields in an array to parse
            $data = array(
                'amount' => $request->amount,
                'biller_name' => $request->biller_name,
                'country' => $request->country,
                'customer' =>  $request->customer,
                'package_data' => $request->package_data,
                'recurrence' => $request->recurrence,
                'reference' => "PBM-". Str::random(7),
                'type' =>  $request->type
            );
            
            $payment = (object) FlutterwaveController::create_bill_payment($data);

            if ($payment->status) {
                $create_transaction = Transaction::create([
                    // 'transaction_ref_num' => $payment->data->BPUSSD1583957963415840,
                    'transaction_type' => 'data',
                    'modules' => $request->package_data,
                    'currency' => $request->country,
                    // 'amount' =>  $payment->data ?  $payment->data : null,
                    'status' => 'successful',
                    'response_status' => $payment->status ? $payment->status : null,
                    'response_message' => $payment->message ? $payment->message : null,
                ]);

                return $create_transaction;
                // if (!$create_transaction) {
                //     return response_data(false, 422, "Sorry an Error Ocurred", false, false, false);
                // }
                // return response_data(true, 200, $request->package_data . ' payment succesful.', false, false, false);
            } 
            
            // else {

            //     $create_transaction = Transaction::create([
            //         'transaction_ref_num' => $payment->data->BPUSSD1583957963415840,
            //         'transaction_type' => 'Subscription',
            //         'modules' => $request->package_data,
            //         'currency' => $request->country,
            //         'amount' => $payment->data->amount,
            //         'status' => 'Failed',
            //         'response_status' => $payment->status,
            //         'response_message' => $payment->message
            //     ]);
            //     if (!$create_transaction) {
            //         return response_data(false, 422, "Sorry an Error Ocurred", false, false, false);
            //     }
            //     return response_data(true, 200, $payment->message, false, false, false);
            // }
        // } else {
        // }
    }
}
