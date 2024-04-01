<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Register;
use App\Models\User;
use App\Traits\Api\V1\Responses;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use \Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;


class Auth extends Controller
{
    use Responses;

    // public function __construct()
    // {
    //     $this->middleware('verified', ['except' => ['login' , 'register' , 'logout']]);
    // }

    public function register(Register $request){

        $request->merge([
            'password' => bcrypt($request->password)
        ]);

        $user = User::create($request->all());

        $this->EmailVerification($user);

        return $this->success(status:Response::HTTP_OK , message:'User created Successfuly.' , data: [$user]);
    }


    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->guard('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->success(status:Response::HTTP_OK , message:'User Retrived Successfuly.' , data: [
            'user' => auth()->guard('api')->user(),
            'token' => $token
        ]);
    }

    public function me()
    {
        if(auth()->guard('api')->user()){
            return $this->success(status:Response::HTTP_OK , message:'User Retrived Successfuly.' , data: [
                'user' => auth()->guard('api')->user(),
            ]);
        }

        return $this->error(status:Response::HTTP_UNAUTHORIZED , message:'Unauthntcation');
    }

    public function logout()
    {
        auth()->guard('api')->logout(true);

        return response()->json(['status' => Response::HTTP_OK ,'message' => 'Successfully logged out'] , Response::HTTP_OK);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        if(auth()->guard('api')->user()){
            return $this->success(status:Response::HTTP_OK , message:'User Retrived Successfuly.' , data: [
                'new_token' => auth()->guard('api')->refresh(),
            ]);
        }

        return $this->error(status:Response::HTTP_INTERNAL_SERVER_ERROR, message:'something went rong');
    }


    public function EmailVerification(User $user){
       return event(new Registered($user));
    }
}
