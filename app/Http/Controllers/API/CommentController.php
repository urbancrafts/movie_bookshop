<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Comments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CommentController extends Controller
{  
    private $ip_address;//private property
    
    
    //create comment input validation method
    public function create_comment(Request $request)
    {
        // `name`,`email`, `blog_id`, `message`, `status`,
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email',
            'book_name' => 'required|string',
            'message' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response_data(false, 422, "Sorry a Validation Error Occured", ['errors' => $validator->errors()->all()], false, false);
        }



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


        
        $name = $request->name;
        $email = $request->email;
        $book_name = $request->book_name;
        $message = $request->message;

        $save_comment = Comments::create([
            'name' => $name,
            'email' => $email,
            'ip_address' => $this->ip_address,
            'book_name' => $book_name,
            'message' => $message,
            'status' => true
        ]);

        if (!$save_comment) {
            return response_data(false, 422, "Sorry! we could not create the comment", false, false, false);
        }

        return response_data(true, 200, "comment created successfully", ['values' => $save_comment], false, false);
    }

    
    
}
