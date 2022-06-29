<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\TwofaEmail;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyCodeMail;
use App\Mail\VerifyCodeTransactionMail;
use App\Http\Controllers\DeviceLogin;
use App\Models\Code;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    // const $token = env('PASSPORT_TOKEN');

    // public $result = new \stdClass();

    public function __construct()
    {
        $this->result = (object)array(
            'status' => false,
            'status_code' => 200,
            'message' => null,
            'data' => (object) null,
            'token' => null,
            'debug' => null
        );
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'name' => 'required|string',
        ]);   

        if ($validator->fails()) {
            $this->result->status = false;
            $this->result->message = "Sorry a Validation Error Occured";
            $this->result->data->errors = $validator->errors()->all();
            $this->result->status_code = 422;
            return response()->json($this->result, 422);
        }

        $email = $request['email'];
        
        $request['password'] = Hash::make($request['password']);
        $request['remember_token'] = Str::random(10);

        $user = User::create($request->toArray());

        if (!$user) {
            $this->result->status = false;
            $this->result->message = "Sorry we could not create account at this time. Try again later";
            $this->result->data->error = ['errors' => ['']];
            $this->result->status_code = 500;
            return response()->json($this->result);
        }

        // add Code to the db 
        $type = 'email_verify';

        $save_code = generate_code($email, $type);

        // send the email code
        Mail::to($email)->send(new VerifyCodeMail($save_code));

        DeviceLogin::check_device_loggedin($email, 'signup');

        $this->result->status = true;
        $this->result->message = "User account created successfully. Please verify your account";
        $this->result->data->user = $user;
        $this->result->status_code = 200;
        return response()->json($this->result, 200);
    }

    public function verify_email_code(Request $request)
    {
        $messages = [
            'required' => 'The :attribute field is required.',
            'string' => "The :attribute field must be a string",
            'exists' => "This :attribute doesn't exist in our records"
        ];

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|exists:App\Models\User,email',
            'code' => 'required|string|max:6',
        ], $messages);

        if ($validator->fails()) {
            $this->result->status = false;
            $this->result->message = "Sorry a Validation Error Occured";
            $this->result->data->errors = $validator->errors()->all();
            $this->result->status_code = 422;
            return response()->json($this->result, 422);
        }

        $code = $request->code;
        $email = $request->email;

        if (code_exists_check($code)) {
            // code exists

            if (code_exists_email_check($email, $code)) {
                // check if the code is for the email address 
                // at this point the code is valid 
                // so we can validate the user and then create a token for the user 

                if (!code_expire_check($code)) {
                    // code is still active 
                    $user = User::where('email', $email)->get();

                    $update_user = $user[0]->update([
                        'is_verified' => true,
                        'email_verified_at' => now(),
                        'status' => 0
                    ]);

                    if (!$update_user) {
                        $this->result->status = false;
                        $this->result->message = "Sorry we could not verify the account. Try Again Later";
                        $this->result->status_code = 422;
                        return response()->json($this->result, 422);
                    } else {
                        // delete the code 
                        delete_code($code);
                        DeviceLogin::check_device_loggedin($email, 'first_time_login');
                        $this->result->status = true;
                        $this->result->message = "Account Verified Successfully";
                        $this->result->status_code = 200;
                        return response()->json($this->result, 200);
                    }
                } else {
                    $this->result->status = false;
                    $this->result->message = "Sorry Code has expired.";
                    $this->result->status_code = 422;
                    return response()->json($this->result, 422);
                }
            } else {
                $this->result->status = false;
                $this->result->message = "Sorry, Invalid Code for this Email Address.";
                $this->result->status_code = 422;
                return response()->json($this->result, 422);
            }
        } else {
            // sorry the code doesnt exist in our records 
            $this->result->status = false;
            $this->result->message = "Sorry Invalid Code.";
            $this->result->status_code = 422;
            return response()->json($this->result, 422);
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        //valid credential
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:50'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }
        //Request is validated
        // Create token
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response_data(false, 400, "Login credentials are invalid.", false, false, false);
            }
        } catch (JWTException $e) {
            // return $credentials;
            return response_data(false, 500, "Could not create token.", false, false, false);
        }

        // at this point we check if the user has 2fa 

        $email = $request->email;

        $user = User::where('email', $email)->first();

        $phone = $user->phone ? $user->phone : null; 
        
        if(_2fa_check($email) && $phone !== null){
            // send sms for verification 

            $type = "email_2fa_verification";
            $this->generate_short_code($email,$type);
            $code = $this->generate_short_code($email,$type)->data->value;

            if(_sms_status_check($email)){
                $message = "Your One-Time-login code is " . $code ;
                SmsController::SendPlainSMS($user->phone, $message);
            }

            return response_data(true, 200, "2-Factor Authentication has been activated on your account. Please check your email/phone for your 6-digit code", ['values' => $user], $token, false);
        }else{
            return response_data(true, 200, "Please complete your account profile. Contact Admin for more Information", ['values' => $user], $token, false);
        }

        DeviceLogin::check_device_loggedin($email, 'login');

        return response_data(true, 200, "Login successful", ['values' => $user], $token, false);
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken(auth('api')->refresh());
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        return response()->json(auth()->user());
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken(Request $request)
    {
        return response()->json([
            'access_token' => $request->token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }


    public function generate_code(Request  $request)
    {
        $email = $request->email;
        $type = $request->type;

        $messages = [
            'required' => 'The :attribute field is required.',
            'string' => "The :attribute field must be a string",
            'exists' => "This :attribute doesn't exist in our records"
        ];

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|exists:App\Models\User,email',
            'type' => 'required|string',
        ], $messages);


        if ($validator->fails()) {
            $this->result->status = false;
            $this->result->message = "Sorry a Validation Error Occured";
            $this->result->data->errors = $validator->errors()->all();
            $this->result->status_code = 422;
            return response()->json($this->result, 422);
        }

        $code = generate_code($email, $type);

        if (!$code) {
            $this->result->status = false;
            $this->result->message = "Sorry we could not generate 6-digit code at this time. Try again later";
            $this->result->status_code = 500;
            return response()->json($this->result);
        }

        switch ($type) {
            case 'email_verify':
                # code...
                // send the email code
                Mail::to($email)->send(new VerifyCodeMail($code));
                break;
            case 'email_paystack_transaction_verify':
                # code...
                // send the email code
                Mail::to($email)->send(new VerifyCodeTransactionMail($code));
                break;
            case 'email_btc_transaction_verify':
                # code...
                // send the email code
                Mail::to($email)->send(new VerifyCodeTransactionMail($code));
                break;
            case 'email_usdt_transaction_verify':
                # code...
                // send the email code
                Mail::to($email)->send(new VerifyCodeTransactionMail($code));
                break;
            default:
                # code...
                // send the email code
                Mail::to($email)->send(new VerifyCodeMail($code));
                break;
        }


        $this->result->status = true;
        $this->result->message = "6-digit code has been generated successfully.";
        $this->result->status_code = 200;
        return response()->json($this->result, 200);
    }

    public function generate_short_code($email,$type)
    {
        $messages = [
            'required' => 'The :attribute field is required.',
            'string' => "The :attribute field must be a string",
            'exists' => "This :attribute doesn't exist in our records"
        ];

        $validator = Validator::make(['email' => $email, 'type' => $type], [
            'email' => 'required|string|email|max:255|exists:App\Models\User,email',
            'type' => 'required|string',
        ], $messages);


        if ($validator->fails()) {
            $this->result->status = false;
            $this->result->message = "Sorry a Validation Error Occured";
            $this->result->data->errors = $validator->errors()->all();
            $this->result->status_code = 422;
            return response()->json($this->result, 422);
        }

        $code = generate_code($email, $type);

        if (!$code) {
            $this->result->status = false;
            $this->result->message = "Sorry we could not generate 6-digit code at this time. Try again later";
            $this->result->status_code = 500;
            return response()->json($this->result);
        }

        // switch ($type) {
        //     case 'email_verify':
        //         # code...
        //         // send the email code
        //         Mail::to($email)->send(new VerifyCodeMail($code));
        //         break;
        //     case 'email_paystack_transaction_verify':
        //         # code...
        //         // send the email code
        //         Mail::to($email)->send(new VerifyCodeTransactionMail($code));
        //         break;
        //     case 'email_btc_transaction_verify':
        //         # code...
        //         // send the email code
        //         Mail::to($email)->send(new VerifyCodeTransactionMail($code));
        //         break;
        //     case 'email_usdt_transaction_verify':
        //         # code...
        //         // send the email code
        //         Mail::to($email)->send(new VerifyCodeTransactionMail($code));
        //         break;
        //     case 'email_2fa_verification':
        //             # code...
        //             // send the email code
        //         Mail::to($email)->send(new TwofaEmail($code));
        //     break;
        //     default:
        //         # code...
        //         // send the email code
        //         Mail::to($email)->send(new VerifyCodeMail($code));
        //         break;
        // }


        $this->result->status = true;
        $this->result->message = "6-digit code has been generated successfully.";
        $this->result->status_code = 200;
        $this->result->data = $code;
        
        // return response()->json($this->result, 200);
        return $this->result;
    }
}
