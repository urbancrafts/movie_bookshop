<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;

class CloudinaryController extends Controller
{
    //

    public function upload_image($image){
        return Cloudinary::upload($image->getRealPath())->getSecurePath();
    }

    public function upload_file($file){
        return Cloudinary::uploadFile($file->file('file')->getRealPath())->getSecurePath();
    }
}
