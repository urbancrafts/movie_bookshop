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
            <h3>An attempt to login to your Account has been made.  </h3>
            <p>Please confirm that you authorized the login. </p> 
            <p>Ip Address : {{get_ip_address()}} || {{_ip()}}</p>
            <p>Here is your 6-digit verification code: <b style="font-size: 20px;"> {{$data->value}}</b></p>
            <p>Your 6-digit verification code will expire on <b style="font-size: 20px;">{{$data->expiry_date}}<b></p>
        </div>
    </div>
@endsection