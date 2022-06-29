<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeviceLogin;
use App\Mail\DeviceLoginEmail;
use App\Http\Controllers\EmailController;

class DeviceLoginController extends Controller
{
    //

private $ip_address,$mac_address,$location,$device_type,$user_agent;

public function __construct(){
        //Buffering the output     
  ob_start();  
  //Getting configuration details 
  system('ipconfig /all');  
  //Storing output in a variable 
  $configdata=ob_get_contents();  
  // Clear the buffer  
  ob_clean();  
  //Extract only the physical address or Mac address from the output
  $mac = "Physical";  
  $pmac = strpos($configdata, $mac);
  // Get Physical Address  
  $this->mac_address=substr($configdata,($pmac+36),17);  

  //whether ip is from share internet
if (isset($_SERVER['HTTP_CLIENT_IP']))   
{
    $this->ip_address = preg_replace('#[^0-9.:]#', '', $_SERVER['HTTP_CLIENT_IP']);
}
//whether ip is from proxy
else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))  
{
    $this->ip_address = preg_replace('#[^0-9.:]#', '', $_SERVER['HTTP_X_FORWARDED_FOR']);
}
else if(isset($_SERVER['HTTP_X_FORWARDED'])){
    $this->ip_address = preg_replace('#[^0-9.:]#', '', $_SERVER['HTTP_X_FORWARDED']);
}
else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
{
    $this->ip_address = preg_replace('#[^0-9.:]#', '', $_SERVER['HTTP_FORWARDED_FOR']);
}
else if(isset($_SERVER['HTTP_FORWARDED'])){
    $this->ip_address = preg_replace('#[^0-9.:]#', '', $_SERVER['HTTP_FORWARDED']);
}
//whether ip is from remote address
else
{
    $this->ip_address = preg_replace('#[^0-9.:]#', '', $_SERVER['REMOTE_ADDR']);
}

       //$guest_ip = preg_replace('#[^0-9.:]#', '', getenv('REMOTE_ADDR'));
       
       //$browser = $_SERVER['HTTP_USER_AGENT'];
       
       //$url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];



   }

//check os information in USER_AGENT strings
public static function systemInfo(){
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $os_platform    = "Unknown OS Platform";
    $os_array       = array('/windows phone 8/i'    =>  'Windows Phone 8',
                            '/windows phone os 7/i' =>  'Windows Phone 7',
                            '/windows nt 6.3/i'     =>  'Windows 8.1',
                            '/windows nt 6.2/i'     =>  'Windows 8',
                            '/windows nt 6.1/i'     =>  'Windows 7',
                            '/windows nt 6.0/i'     =>  'Windows Vista',
                            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                            '/windows nt 5.1/i'     =>  'Windows XP',
                            '/windows xp/i'         =>  'Windows XP',
                            '/windows nt 5.0/i'     =>  'Windows 2000',
                            '/windows me/i'         =>  'Windows ME',
                            '/win98/i'              =>  'Windows 98',
                            '/win95/i'              =>  'Windows 95',
                            '/win16/i'              =>  'Windows 3.11',
                            '/macintosh|mac os x/i' =>  'Mac OS X',
                            '/mac_powerpc/i'        =>  'Mac OS 9',
                            '/linux/i'              =>  'Linux',
                            '/ubuntu/i'             =>  'Ubuntu',
                            '/iphone/i'             =>  'iPhone',
                            '/ipod/i'               =>  'iPod',
                            '/ipad/i'               =>  'iPad',
                            '/android/i'            =>  'Android',
                            '/blackberry/i'         =>  'BlackBerry',
                            '/webos/i'              =>  'Mobile');
    $found = false;
    $addr = new RemoteAddress;
    $device = '';
    foreach ($os_array as $regex => $value) 
    { 
        if($found)
         break;
        else if (preg_match($regex, $user_agent)) 
        {
            $os_platform    =   $value;
            $device = !preg_match('/(windows|mac|linux|ubuntu)/i',$os_platform)
                      ?'MOBILE':(preg_match('/phone/i', $os_platform)?'MOBILE':'SYSTEM');
        }
    }
    $device = !$device? 'SYSTEM':$device;
    return array('os'=>$os_platform,'device'=>$device);

}

//check browser information in USER_AGENT strings
public static function browser(){
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    $browser        =   "Unknown Browser";

    $browser_array  = array('/msie/i'       =>  'Internet Explorer',
                            '/firefox/i'    =>  'Firefox',
                            '/safari/i'     =>  'Safari',
                            '/chrome/i'     =>  'Chrome',
                            '/opera/i'      =>  'Opera',
                            '/netscape/i'   =>  'Netscape',
                            '/maxthon/i'    =>  'Maxthon',
                            '/konqueror/i'  =>  'Konqueror',
                            '/mobile/i'     =>  'Handheld Browser');

    foreach ($browser_array as $regex => $value) 
    { 
        if($found)
         break;
        else if (preg_match($regex, $user_agent,$result)) 
        {
            $browser    =   $value;
        }
    }
    return $browser;
 
}


public static function get_location(){

//Uses ipinfo.io to get the location of the IP Address.
$json     = file_get_contents("http://ipinfo.io/".$this->ip_address."/geo");
//Breaks down the JSON object into an array
$json     = json_decode($json, true);
//This variable is the visitor's county
//$country  = $json['country'];
//This variable is the visitor's region
//$region   = $json['region'];
//This variable is the visitor's city
//$city     = $json['city'];

return $json;
}

//execute this method to check device loggedin history
public static function check_device_loggedin($email, $event){
    $systeminfo = self::systemInfo();//call device systeminfo method
    $os = $systeminfo['os'];//call the os object of the systeminfo method returned array
    $device = $systeminfo['device'];//call the device object of the systeminfo method returned array
    
    $browser = self::browser();//call browser method

    $location = self::get_location();//call get_location method
    $location_data = array('city' => $location['city'],
                           'region' => $location['region'],
                           'country' => $location['country']
                             );
    
    //$location['city'].', '.$location['region'].', '.$location['country'];

    $check_devices = DeviceLogin::where('loggedin_email', $email)->get();
    if(count($check_devices) > 0){
      foreach($check_devices as $device_info){
          if($os != $device_info->os || $device != $device_info->device_type ){
            $data = array('changed' => 'device',
                          'ip_address' => $this->ip_address,
                          'mac_address' => $this->mac_address,
                          'os' => $os,
                          'device_type' => $device,
                          'browser' => $browser,
                          'location' => $location_data);
            $mail = EmailController::device_detection($data);
          }else if($browser != $device_info->browser){
            $data = array('changed' => 'browser',
                          'ip_address' => $this->ip_address,
                          'mac_address' => $this->mac_address,
                          'os' => $os,
                          'device_type' => $device,
                          'browser' => $browser,
                          'location' => $location_data);
            $mail = EmailController::device_detection($data);
          }else if($this->ip_address != $device_info->device_ip){
            $data = array('changed' => 'ip_address',
                          'ip_address' => $this->ip_address,
                          'mac_address' => $this->mac_address,
                          'os' => $os,
                          'device_type' => $device,
                          'browser' => $browser,
                          'location' => $location_data);
            $mail = EmailController::device_detection($data);
          }else if($this->mac_address != $device_info->mac_address){
            $data = array('changed' => 'mac_address',
                          'ip_address' => $this->ip_address,
                          'mac_address' => $this->mac_address,
                          'os' => $os,
                          'device_type' => $device,
                          'browser' => $browser,
                          'location' => $location_data);
            $mail = EmailController::device_detection($data);
        }else if($location_data != $device_info->location_strings){
            $data = array('changed' => 'location',
                          'ip_address' => $this->ip_address,
                          'mac_address' => $this->mac_address,
                          'os' => $os,
                          'device_type' => $device,
                          'browser' => $browser,
                          'location' => $location_data);
            $mail = EmailController::device_detection($data);
        }else{
        
        $insert = DeviceLogin::create(['loggedin_email' => $email,
                                       'device_ip' => $this->ip_address,
                                       'mac_address' => $this->mac_address,
                                       'location_strings' => $location_data,
                                       'os' => $os,
                                       'device_type' => $device,
                                       'browser' => $browser,
                                       'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                                       'event' => $event]);
        
    
          }
      }
    }else{
        $insert = DeviceLogin::create(['loggedin_email' => $email,
                                       'device_ip' => $this->ip_address,
                                       'mac_address' => $this->mac_address,
                                       'location_strings' => $location_data,
                                       'os' => $os,
                                       'device_type' => $device,
                                       'browser' => $browser,
                                       'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                                       'event' => $event]);
    }
    
} 



}
