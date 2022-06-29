@extends('layouts.mail')

@section('content')
    <style type="text/css" rel="stylesheet">
        .logo{
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: row;
            height: 50px;
            width: 100px;
            margin: 0 auto;
        }
    </style>
    <div class="container-fluid">
        <div class="row">
            <img src="https://docs.paybuymax.com/paybuymax_logo.png" style="height: 100px; width:500px; margin:0 auto; display:flex; flex-direction:row; justify-content:center; align-content:center;" class="img logo img-responsive mx-auto" alt="Paybuymax Logo"/>
        </div>
        <div class="row">
            
            <h3>Thanks for Registering with Paybuymax. </h3>
            <p>Please verify this transaction in your account. </p> 
            <p>Here is your 6-digit verification code: <strong style="font-size: 18px;"> {{$data->value}}</strong></p>
            <p>Your 6-digit verification code will expire on <strong style="font-size: 18px;">{{$data->expiry_date}}<strong></p>
        </div>
    </div>
@endsection