<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Http;

class InternalToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(request()->bearerToken()){
            $me = Http::withHeaders([
                'Authorization' => 'Bearer '.request()->bearerToken(),
                'Accept' => 'application/json'
            ])->get(env('INTERNAL_URL').'/api/v1/me');

            if($me->successful()){
                // $data = json_decode($me->body() , true);
                // $admin_id = $data['data']['admin']['id'];
                // $request->attributes->set('admin_id', $admin_id);

                return $next($request);
            }

            return response()->json([
                'status' => HttpResponse::HTTP_FORBIDDEN,
                'message' => 'Token invalied',
            ] , HttpResponse::HTTP_FORBIDDEN);
        }


        return response()->json([
            'status' => HttpResponse::HTTP_FORBIDDEN,
            'message' => 'Token Not Found',
        ] , HttpResponse::HTTP_FORBIDDEN);
    }
}
