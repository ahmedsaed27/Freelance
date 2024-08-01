<?php

namespace App\Http\Middleware;

use App\Models\Verification;
use App\Traits\Api\V1\Responses;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Symfony\Component\HttpFoundation\Response;

class VerificationProfile
{
    use Responses;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->guard('api')->user();
        $profile = $user->profile;

        if(!$profile){
            $this->error(status:500 , message:'User Dosnt Have Profile.');
        }

        if(Verification::where('profile_id' , $profile->id)->exists()){
            return $this->error(HttpResponse::HTTP_INTERNAL_SERVER_ERROR , message:'Your Profile Alredy Verified.');
        }

        return $next($request);
    }
}
