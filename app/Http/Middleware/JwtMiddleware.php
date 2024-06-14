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
                return response()->json([ 'status' => status::HTTP_FORBIDDEN , 'message' => 'Token is Invalid'], status::HTTP_FORBIDDEN);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(['status' => status::HTTP_FORBIDDEN , 'message' => 'Token is Expired'] , status::HTTP_FORBIDDEN);
            } else {
                return response()->json(['status' => status::HTTP_FORBIDDEN ,'message' => 'Authorization Token not found'] , status::HTTP_FORBIDDEN);
            }
        }
        return $next($request);
    }
}
