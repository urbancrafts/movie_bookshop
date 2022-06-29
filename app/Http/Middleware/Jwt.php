<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    
    public function handle($request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json(
                    [
                        'status' => false,
                        'status_code' => 200,
                        'message' => 'Token is Invalid',
                        'data' => (object) null,
                        'token' => null,
                        'debug' => null
                    ]);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return response()->json(
                    [
                        'status' => false,
                        'status_code' => 200,
                        'message' => 'Token is Expired',
                        'data' => (object) null,
                        'token' => null,
                        'debug' => null
                    ]);
            }else{
                return response()->json(
                    [
                        'status' => false,
                        'status_code' => 200,
                        'message' => 'Authorization Token not found',
                        'data' => (object) null,
                        'token' => null,
                        'debug' => null
                    ]);
            }
        }
        return $next($request);
    }
}