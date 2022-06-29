<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use App\Models\Settings;
use App\Models\UserVerification;
use App\Http\Controllers\PHPMailerController;

class EmailController extends Controller
{
    //

    public function __construct(){	
    }

    public static function send($user, $title, $content){
        
        $mail = new PHPMailerController(); // defaults to using php "mail()"
        $siteEmailDomain = Settings::where('id', 1)->get();
        $siteEmail = 'noreply@'.str_replace('https://', '', str_replace('http://', '', str_replace('www.', '', $siteEmailDomain[0]->site_url)));
        $siteName = $siteEmailDomain[0]->site_name;

        $mail->SetFrom($siteEmail, $siteName);

        $address = $user->email;

        if ($user->name) {
            $full_name = $user->name;
        } else {
            $full_name = $user->first_name.' '.$user->last_name;
        }
        $mail->AddAddress($address, $full_name);
        $mail->Subject = $title;
        $mail->MsgHTML($content);

        $mail->Send();

    }


    public static function signupVerification($user=array()) {
        
        //$controller = new controller();
        $emailContent = EmailTemplate::where('id', 1)->get()->first();
        $verifyCode = UserVerification::insertEmailCode($user->id)->email_code;
        //
        $siteInfo = Settings::single();

        $search = array('{NAME}', '{EMAIL_CODE}');
        $replace = array($user->name, $verifyCode);

        $body = str_replace($search, $replace, $emailContent->content);
        //
        $htmlbody = view('emails.generic', [
            'receiverName' => $user->name,
            'siteInfo' => $siteInfo,
            'title' => $emailContent->title,
            'emailContent' => $body,
            'signature' => $emailContent->footer
        ]);

        self::send($user, $emailContent->title, $htmlbody);

    }


    public static function userWelcome($user=array()){
        //$controller = new controller();
        $emailContent = EmailTemplate::where('id', 2)->get()->first();
        //$verifyCode = UserVerification::insertEmailCode($user->id)->email_code;
        //
       $siteInfo = Settings::single();
      
       $siteName = $siteInfo->site_name;
       //$expire = date('d/m/Y', $data->expire);

       $search = array('{NAME}', '{SITE_NAME}');
       $replace = array($user[0]->name, $siteName);

       $body = str_replace($search, $replace, $emailContent->content);
       //
       $htmlbody = view('emails.generic', [
           'receiverName' => $user[0]->name,
           'siteInfo' => $siteInfo,
           'title' => $emailContent->title,
           'emailContent' => $body,
           'signature' => $emailContent->footer
       ]);

       self::send($user[0], $emailContent->title, $htmlbody);
   }


   public static function device_detection($data=array()){
       //$controller = new controller();
       $emailContent = EmailTemplate::where('id', 1)->get()->first();
       
       //
       $siteInfo = Settings::single();

       $header_msg = "";
       //check the changed array object to get its returned value
       if($data->changed = "device"){
       $header_msg = "We have detected an account sign-in request from a device we do not recorgnize"; 
       }else if($data->changed = "browser"){
        $header_msg = "We have detected an account sign-in request from a browser we do not recorgnize"; 
       }else if($data->changed = "ip_address"){
        $header_msg = "We have detected an account sign-in request with an ip address we do not recorgnize"; 
       }else if($data->changed = "mac_address"){
        $header_msg = "We have detected an account sign-in request from a device we do not recorgnize"; 
       }else if($data->changed = "location"){
        $header_msg = "We have detected an account sign-in request from a location we do not recorgnize"; 
       }


       $search = array('{CHANGED_DEVICE_MSG}', '{IP_ADDRESS}', '{MAC_ADDRESS}', '{OS}', '{DEVICE}', '{BROWSER}', '{CITY}', '{REGION}', '{COUNTRY}');
       $replace = array($header_msg, $data->ip_address, $data->mac_address, $data->os, $data->device_type, $data->browser, $data->location->city, $data->location->region, $data->location->country);

       $body = str_replace($search, $replace, $emailContent->content);
       //
       $htmlbody = view('mails.device_login', [
           //'changedDeviceMSG' => $header_msg,
           'siteInfo' => $siteInfo,
           'title' => $emailContent->title,
           'emailContent' => $body,
           'signature' => $emailContent->footer
       ]);

       self::send($user, $emailContent->title, $htmlbody);
   }



    public static function orderConfirmation($orderInfo, $orders=array(), $recommended=array()) {
        $controller = new controller();
        $emailContent = Email_templates::where('id', 6)->get()->first();

        $siteInfo = WebsiteSettings::single();
        $currency = CurrencyTbl::getDefault();
        $brands = BrandTbl::take(4)->get();
        $siteName = $siteInfo->biz_name;

        $search = array('{FULL_NAME}');
        $replace = array($orderInfo['full_name']);
        $body = str_replace($search, $replace, $emailContent->content);

        $htmlbody = $controller->buildView('emails/orderConfirmation', [
            'receiverName' => $orderInfo['full_name'],
            'siteInfo' => $siteInfo,
            'title' => $emailContent->title,
            'emailContent' => $body,
            'signature' => $emailContent->footer,
            'orderInfo' => $orderInfo,
            'cur' => $currency,
            'orders' => $orders,
            'recommended' => $recommended,
            'brands' => $brands
        ]);

        self::send($orderInfo, $emailContent->title, $htmlbody);
    }



    public static function passwordResetEmail($user=array()) {
        
        $emailContent = EmailTtemplate::where('id', 2)->get()->first();
        $verifyCode = User_verification::insertEmailCode($user->login_id)->email_code;
        //
        $siteInfo = WebsiteSettings::single();

        //
        $search = array('{NAME}', '{EMAIL_CODE}');
        $replace = array($user->full_name, $verifyCode);

        $body = str_replace($search, $replace, $emailContent->content);
        //
        $htmlbody = $controller->buildView('emails/generic', [
            'receiverName' => $user->full_name,
            'siteInfo' => $siteInfo,
            'title' => $emailContent->title,
            'emailContent' => $body,
            'signature' => $emailContent->footer
        ]);

        self::send($user, $emailContent->title, $htmlbody);
    }


    public static function emailRest($user=array()) {
        $controller = new controller();
        $emailContent = Email_templates::where('id', 4)->get()->first();
        $verifyCode = User_verification::insertEmailCode($user->login_id)->email_code;
        //
        $siteInfo = WebsiteSettings::single();

        //
        $search = array('{NAME}', '{VERIFY_EMAIL}', '{VERIFY_URL}');
        $replace = array($user->full_name, $verifyCode, $verifyCode);

        $body = str_replace($search, $replace, $emailContent->content);
        //
        $htmlbody = $controller->buildView('emails/generic', [
            'receiverName' => $user->full_name,
            'siteInfo' => $siteInfo,
            'title' => $emailContent->title,
            'emailContent' => $body,
            'signature' => $emailContent->footer
        ]);

        self::send($user, $emailContent->title, $htmlbody);
    }



    public static function sellerPasswordReset($seller=array()) {
        $controller = new controller();
        $emailContent = Email_templates::where('id', 2)->get()->first();
        $verifyCode = SellerVerification::insertEmailCode($seller->seller_id)->email_code;
        //
        $siteInfo = WebsiteSettings::single();

        $search = array('{NAME}', '{EMAIL_CODE}');
        $replace = array($seller->first_name.' '.$seller->last_name, $verifyCode);

        $body = str_replace($search, $replace, $emailContent->content);
        //
        $htmlbody = $controller->buildView('emails/generic', [
            'receiverName' => $seller->first_name.' '.$seller->last_name,
            'siteInfo' => $siteInfo,
            'title' => $emailContent->title,
            'emailContent' => $body,
            'signature' => $emailContent->footer
        ]);

        self::send($seller, $emailContent->title, $htmlbody);
    }





    


    public static function userSubscription($seller=array(), $data=array()) {
        $controller = new controller();
        $emailContent = Email_templates::where('id', 9)->get()->first();
        //$verifyCode = SellerVerification::insertEmailCode($seller->seller_id)->email_code;
        
        //
        $siteInfo = WebsiteSettings::single();

        $expire = date('d/m/Y', $data->expire);

        $search = array('{NAME}', '{PLAN}', '{EXPIRE}', '{REFERENCE}', '{GATEWAY}', '{AMOUNT}', '{STATUS}');
        $replace = array($seller->first_name.' '.$seller->last_name, $data->plan, $expire, $data->ref_id, $data->gateway, $data->currency.$data->amount, $data->status);

        $body = str_replace($search, $replace, $emailContent->content);
        //
        $htmlbody = $controller->buildView('emails/generic', [
            'receiverName' => $seller->first_name.' '.$seller->last_name,
            'siteInfo' => $siteInfo,
            'title' => $emailContent->title,
            'emailContent' => $body,
            'signature' => $emailContent->footer
        ]);

        self::send($seller, $emailContent->title, $htmlbody);
    }


    


    public static function userSubscriptionExpiration($seller=array(), $data=array()) {
        $controller = new controller();
        $emailContent = Email_templates::where('id', 11)->get()->first();
        //$verifyCode = SellerVerification::insertEmailCode($seller->seller_id)->email_code;
        
        //
        $siteInfo = WebsiteSettings::single();
        $siteName = $siteInfo->biz_name;
        $expire = date('d/m/Y', $data->expire);

        $search = array('{NAME}', '{PLAN}', '{EXPIRE}', '{AMOUNT}');
        $replace = array($seller->first_name.' '.$seller->last_name, $data->plan, $expire, $data->currency.$data->amount);

        $body = str_replace($search, $replace, $emailContent->content);
        //
        $htmlbody = $controller->buildView('emails/generic', [
            'receiverName' => $seller->first_name.' '.$seller->last_name,
            'siteInfo' => $siteInfo,
            'title' => $emailContent->title,
            'emailContent' => $body,
            'signature' => $emailContent->footer
        ]);

        self::send($seller, $emailContent->title, $htmlbody);
    }

    // public function sellerPasswordReset($seller=array()) {
 //    	$controller = new controller();
 //    	$verifyCode = Settings::randomStrgs(100);
    // 	$info = Seller::where('seller_id', $seller->seller_id)->update([
    // 		'email_code' => $verifyCode,
    // 		'email_verify' => 0,
    // 		'email_timer' => CustomDateTime::addMinute(30)
    // 	]);

 //        // $body = str_replace($search, $replace, $emailContent->content);
 //        $title = 'Password Reset';
 //        $body = "Hello <b>".$seller->first_name. " ". $seller->last_name."</b>,  <br><br> Click on the link below to reset your password.<br><br> <a href='".Config::SELLER_BASE_URL()."verification/retrieve_password/".$seller->email."/".$verifyCode."'>".Config::SELLER_BASE_URL()."verification/retrieve_password/".$seller->email."/".$verifyCode."</a> <br><br> Thank You!";

 //        self::send($seller, $title, $body);
    // }



    public static function email_verification($user=array()) {
        $controller = new controller();
        $emailContent = Email_templates::where('id', 4)->get()->first();
        $verifyCode = User_verification::insertEmailCode($user->login_id)->email_code;
        //
        $siteInfo = WebsiteSettings::single();
        $resetURL = Config::domain()."verification/$verifyCode";
        $verifyEMail = '<a href="'.$resetURL.'" class="btn-primary" style="font-family: \'Helvetica Neue\',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background-color: #e23e1d; margin: 0; border-color: #e23e1d; border-style: solid; border-width: 10px 20px;">Verify Now</a>';

        //
        $search = array('{NAME}', '{VERIFY_EMAIL}', '{VERIFY_URL}');
        $replace = array($user->full_name, $verifyEMail, $resetURL);

        $body = str_replace($search, $replace, $emailContent->content);
        //
        $htmlbody = $controller->buildView('emails/generic', [
            'receiverName' => $user->full_name,
            'siteInfo' => $siteInfo,
            'title' => $emailContent->title,
            'emailContent' => $body,
            'signature' => $emailContent->footer
        ]);

        self::send($user, $emailContent->title, $htmlbody);
    }


    public static function chatEmail($receiverInfo=array(), $senderInfo=array()) {
        $controller = new controller();

        $emailContent = Email_templates::where('id', 3)->get()->first();
        //
       $search = array('{USERNAME}', '{USER_FIRSTNAME}', '{USER_LASTNAME}', '{USER_EMAIL}', '{MESSAGE}', '{RECEIVER_USERNAME}');

        $replace = array(ucwords($receiverInfo->username), $receiverInfo->first_name, $receiverInfo->last_name, $receiverInfo->email, $msg, ucwords($senderInfo->username));
        //
        $subjectReplace = array(ucwords($senderInfo->username), $senderInfo->first_name, $senderInfo->last_name, $senderInfo->email, '', ucwords($senderInfo->username));
        //
        $subject = str_replace($search, $subjectReplace, $emailContent->title);
        $body = str_replace($search, $replace, $emailContent->content);
        //
        $htmlbody = $controller->buildView('emails/emailView', [
            'receiverName' => $receiverInfo->first_name.' '.$receiverInfo->last_name,
            'title' => $subject,
            'emailContent' => $body,
            //'linkRole' => 'Verify',
            //'linkRole' => 'Password Reset',
            //'linkRole' => 'Login',
            'linkRole' => 'Message',
            'link' => Config::USER_BASE_URL().'inbox/'.$receiverInfo->username,
            'signature' => $emailContent->footer
        ]);

        self::send($receiverInfo, $subject, $htmlbody);
    }

}
