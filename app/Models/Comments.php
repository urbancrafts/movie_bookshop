<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class Comments extends Model
{
    use HasFactory;


    protected $fillable = [ 
        'id',   
        'name',
        'email',
        'ip_address',
        'book_name',
        'message',
        'status',
        'created_at',
        'updated_at'
    ];
    
    protected $table = 'comments';
    protected $casts = [ 
        'id' => 'integer', 
    ];
    protected $primaryKey = 'id';
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    
}
