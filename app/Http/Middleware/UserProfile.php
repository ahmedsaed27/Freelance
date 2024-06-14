<?php

namespace App\Http\Middleware;

use App\Models\Profiles;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Symfony\Component\HttpFoundation\Response;

class UserProfile
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(Profiles::where('user_id' , auth()->guard('api')->id())->exists()){
            return Response()->json([
                'status' => HttpResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'You Have Already Profile'
            ],HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $next($request);
    }
}
