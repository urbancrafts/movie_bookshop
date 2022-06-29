<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BankAccountDetail;
use App\Models\notification;
use App\Models\support;
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

class SupportController extends Controller
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
    //fetch all support tickect
    public function index()
    {
        $support = support::orderBy('id', 'desc')->get();
        if(count($support) > 0){

            return response_data(true, 200, 'Fetched all support.', ['values' => $support], false, false);
        }else{
            return response_data(false, 422, 'No data fetched', false, false, false); 
        }  
    }

    //read a single support ticket with support table id 
    public function read_support_ticket($id){

        $support = support::where('id', $id)->get();
        if(count($support) > 0){
           $update_support = support::where('id', $support[0]->id)->update(['read' => 1]);
            return response_data(true, 200, 'Support ticket fetched.', ['values' => $support], false, false);
        }else{
            return response_data(false, 422, 'No data fetched', false, false, false); 
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
    {//store
        $credentials = $request->only('name', 'email', 'support_ticket', 'tag', 'message', 'attachment');

        //valid credential
        $validator = Validator::make($credentials, [
            'name' => 'nullable|string',
            'email' => 'nullable',
            'support_ticket' => 'nullable',
            'tag' => 'nullable',
            'message' => 'nullable',
            'attachment' => 'nullable',
        ]);
         //check if there's validation error 
        if ($validator->fails()) {
            return response_data(false, 422, "Sorry a Validation Error Occured", ['errors' => $validator->errors()->all()], false, false);
        }

    if($this->user){
   $support = $this->user->support()->create(['support_ticket' => $request->support_ticket,
                                   'tag' => $request->tag,
                                   'message' => $request->message,
                                   'attachment' => $request->attachment]);
                                   return response_data(true, 200, 'Support ticket posted succesfully.', ['values' => $support], false, false);
    }else{
 
        $support = support::create(['name' => $request->name,
                                    'email' => $request->email,
                                    'support_ticket' => $request->support_ticket,
                                    'tag' => $request->tag,
                                    'message' => $request->message,
                                    'attachment' => $request->attachment
                                    ]);
    return response_data(true, 200, 'Support ticket posted succesfully.', ['values' => $support], false, false);

    }


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
    public function update(Request $request, $id)
    {
        //
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
