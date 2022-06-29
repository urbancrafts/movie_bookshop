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
            <h3>Hello {{$data->email}}!</h3>
            <p>Your Account has been verified successfully. Happy Trading!!!</p>
            <p>You can now login <b><a href="#">here</a></b></p>
        </div>
    </div>
@endsection