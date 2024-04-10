<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\Api\V1\Responses;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Http\Response;


class VerifyEmailController extends Controller
{
    use Responses;
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $user = User::find($request->route('id'));

        if ($user->hasVerifiedEmail()) {
            return redirect(env('FRONT_URL') . '/email/verify/already-success');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

     
        $token = Auth::guard('api')->login($user);

        return $this->success(status:Response::HTTP_OK , message:'email verfied' , data:[
            'token' => $token
        ]);
    }
}
