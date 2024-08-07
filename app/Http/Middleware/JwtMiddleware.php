<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use \Illuminate\Http\Response as status;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json([ 'status' => status::HTTP_INTERNAL_SERVER_ERROR , 'message' => 'Token is Invalid'], status::HTTP_INTERNAL_SERVER_ERROR);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(['status' => status::HTTP_INTERNAL_SERVER_ERROR , 'message' => 'Token is Expired'] , status::HTTP_INTERNAL_SERVER_ERROR);
            } else {
                return response()->json(['status' => status::HTTP_INTERNAL_SERVER_ERROR ,'message' => 'Authorization Token not found'] , status::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
        return $next($request);
    }
}
