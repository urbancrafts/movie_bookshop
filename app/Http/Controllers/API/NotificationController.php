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

class NotificationController extends Controller
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

    //fetch all notification
    public function index()
    {
        $notification = notification::where('user_id', $this->user->id)->orderBy('id', 'desc')->get();
        if(count($notification) > 0){
           
          return response_data(true, 200, 'Fetch all notification.', ['values' => $notification], false, false);
        }else{
            return response_data(false, 422, 'No data fetched', false, false, false); 
        }
    }

    //count unread notifications
    public function count_unread_notification(){
        $notification = notification::where('user_id', $this->user->id)->where('read', 0)->get();
        if(count($notification) > 0){
        return response_data(true, 200, 'unread notifcation count loaded.', ['values' => count($notification)], false, false);
        }else{
            return response_data(false, 422, 'No unread notification count loaded', false, false, false); 
        }  
    }

    //read individual notification
    public function read_notification($id){
        $notification = notification::where('id', $id)->where('user_id', $this->user->id)->get();
        if(count($notification) > 0){
          $update = notification::where('id', $notification[0]->id)->where('user_id', $this->user->id)->update(['read' => 1]);
          return response_data(true, 200, 'Fetched all notification.', ['values' => $notification], false, false);
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
