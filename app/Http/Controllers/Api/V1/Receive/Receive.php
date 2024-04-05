<?php

namespace App\Http\Controllers\Api\V1\Receive;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\Api\V1\Responses;
use Illuminate\Http\Request;
use \Illuminate\Http\Response;

class Receive extends Controller
{
    use Responses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user_receive = User::with('receive')->where('id' , auth()->guard('api')->id())->first();

        return $this->success(status:Response::HTTP_OK , message:'User Retrieved Successfully.' , data:$user_receive);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $caseId = $request->caseId;

        auth()->guard('api')->user()->receive()->attach($caseId);

        $receive = auth()->guard('api')->user()->receive;

        return $this->success(status: Response::HTTP_OK, message: 'User received the case successfully.', data: $receive);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user_receive = User::with('receive')->where('id' , $id)->first();

        if (!$user_receive) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Profile not found.',);
        }

        return $this->success(status: Response::HTTP_OK, message: 'User received the case successfully.', data: $user_receive);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
