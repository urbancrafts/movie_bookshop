<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BankAccountDetail;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\PaystackCustomerController;
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

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $user;
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function index()
    {
        //

        $users = User::where('id', $this->user->id)->where('status', 'Active' )->get();
        $empty_array = array();
        if(count($users) > 0){
           
            $data['profile'] = $users;
            $data['bank_detail'] = $this->user->bank_account_detail()->get();

          array_push($empty_array, $data);
          return response_data(true, 200, 'Profile info fetched.', ['values' => $empty_array], false, false);
        }else{
            return response_data(false, 422, $this->user, false, false, false); 
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $credentials = $request->only('name', 'date_of_birth', 'address', 'poster_code', 'city', 'phone', 'phone_2', 'exchange_id', 'account_name', 'account_number', 'account_type', 'bank_name', 'bvn');

        //valid credential
        $validator = Validator::make($credentials, [
            'name' => 'required|string',
            'date_of_birth' => 'required',
            'address' => 'nullable',
            'poster_code' => 'nullable',
            'city' => 'nullable',
            //'email' => 'required',
            'phone' => 'required',
            'phone_2' => 'nullable',
            'exchange_id' => 'nullable',
            //bank detail form validation
            'account_name' => 'nullable',
            'account_number' => 'nullable',
            'account_type' => 'nullable',
            'bank_name' => 'nullable',
            'bvn' => 'nullable',
        ]);
         //check if there's validation error 
        if ($validator->fails()) {
            return response_data(false, 422, "Sorry a Validation Error Occured", ['errors' => $validator->errors()->all()], false, false);
        }
        //
        //$token = JWTAuth::authenticate($request->token);
        //query user table with jwt token id
        $user_check = User::where('id', $this->user->id)->get();
        //check to see if query is greater than zero
        if(count($user_check) > 0){
            
            //update user table record
            
            $update_user = User::where('id', $this->user->id)->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'phone_2' => $request->phone_2,
                'exchange_id' => $request->exchange_id,
                'date_of_birth' => $request->date_of_birth,
                'address' => $request->address,
                'poster_code' => $request->poster_code,
                'city' => $request->city,
                'is_complete' => true
            ]);

        $check_bank_detail = $this->user->bank_account_detail()->get();//get bank_account_details table
        if(count($check_bank_detail) > 0){//check if user data already exists
            //initiate update query
            $bank_detail = $this->user->bank_account_detail()->update([
                                           'account_name' => $request->account_name,
                                           'account_number' => $request->account_number,
                                           'account_type' => $request->account_type,
                                           'bank_name' => $request->bank_name,
                                           'bvn' => $request->bvn
            ]);
        }else{//if user data does not exit

            //initiate insert query
            $bank_detail = $this->user->bank_account_detail()->create([
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
                'account_type' => $request->account_type,
                'bank_name' => $request->bank_name,
                'bvn' => $request->bvn
]);

        }
            
            return response_data(true, 200, 'Account Successfully Updated.', false, false, false);
        }else{

            return response_data(false, 422, $this->user, false, false, false);
            
        }   
        
    }


    public function verify_paystack_customer(Request $request){
        $credentials = $request->only('account_number', 'bank_code');

        //valid credential
        $validator = Validator::make($credentials, [
            'account_number' => 'required',
            'bank_code' => 'required',
        ]);
         //check if there's validation error 
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }
        PaystackCustomerController::verify_customer($request->account_number, $request->bank_code);
    }


    public function create_bank_detail(Request $request){
        $credentials = $request->only('token', 'account_name', 'account_number', 'account_type', 'bank', 'bvn');

        //valid credential
        $validator = Validator::make($credentials, [
            'token' => 'required',
            'account_name' => 'required|string',
            'account_number' => 'required',
            'account_type' => 'required',
            'bank' => 'required',
            'bvn' => 'nullable',
        ]);
         //check if there's validation error 
        if ($validator->fails()) {
            return response_data(false, 422, "Sorry a Validation Error Occured", ['errors' => $validator->errors()->all()], false, false);
        }


        $token = JWTAuth::authenticate($request->token);
        //query user table with session id
        $user_check = User::where('id', $token->id)->get();
        //check to see if query is greater than zero
        if(count($user_check) > 0){
                $acc_name = $request->account_name;
                $acc_number = $request->account_number;
                $acc_type = $request->account_type;
                $bank = $request->bank;
                $bvn = $request->bvn;
            //query bank_account_details table to see if user record exists
            $check_bank_detail = BankAccountDetail::where('user_id', $user_check[0]->id)->get();

            if(count($check_bank_detail) > 0){//if record exists, update record
                
                $update_bank_detail = $this->user->bank_account_detail()
                                                    ->update(['account_name' => $acc_name,
                                                               'account_number' => $acc_number,
                                                               'account_type' => $acc_type,
                                                               'bank_name' => $bank,
                                                               'bvn' => $bvn]);
                if(!$update_bank_detail){
                    return response_data(false, 422, "Sorry, an error occured while updating record!", false, false, false);
                }
                return response_data(true, 200, 'Bank details updated succesfully.', false, false, false);

                }else{//else, initialize insert query for user
                   $insert_bank_detail = $this->user->bank_account_detail()
                   ->create(['account_name' => $acc_name,
                             'account_number' => $acc_number,
                             'account_type' => $acc_type,
                             'bank_name' => $bank,
                             'bvn' => $bvn]);

                if(!$insert_bank_detail){
                    return response_data(false, 422, "Sorry, an error occured while creating record!", false, false, false);  
                }
                return response_data(true, 200, 'Bank detail created succesfully.', false, false, false);
                }
        }else{
            return response_data(false, 422, $token, false, false, false);     
        }


    }

    public function delete_bank_detail(BankAccountDetail $bank){
         $bank->delete();
         if(!$bank){
            return response_data(false, 422, "Sorry, an error occured while deleting record!", false, false, false);  
         }

         return response_data(true, 200, 'Bank account detail deleted.', false, false, false);
    }




    public function validate_bank_account(Request $request){
        $credentials = $request->only('token', 'first_name', 'last_name', 'account_number', 'account_type', 'bank', 'bvn');

        //valid credential
        $validator = Validator::make($credentials, [
            'token' => 'required',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'account_number' => 'required',
            'account_type' => 'required',
            //'bank' => 'required',
            'bvn' => 'nullable',
        ]);
         //check if there's validation error 
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        $token = JWTAuth::authenticate($request->token);
        //query user table with session id
        $user_check = User::where('id', $token->id)->get();
        //check to see if query is greater than zero
        if(count($user_check) > 0){
        $fname = $request->first_name;
        $lname = $request->last_name;
        $number = $request->account_number;
        $type = $request->account_type;
        //$bank = $request->bank;
        $bvn = $request->bvn;
        $currency = "NGN";

        $check = PaymentController::verifyCustomer($currency, $bvn, $number, $fname, $lname, $bvn);
        if($check->status == true){
        return response_data(true, 200, $check->message, false, false, false);
        }else{
        return response_data(false, 422, $check->message, false, false, false);
        }
        
        }else{
            return response_data(false, 422, $token, false, false, false);  
        }
    }
   



    public function fund_my_ngn_wallet(Request $request){
        
        $token = JWTAuth::authenticate($request->token);
        //query user table with session id
        $user_check = User::where('id', $token->id)->get();
        //check to see if query is greater than zero
        if(count($user_check) > 0){

        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
