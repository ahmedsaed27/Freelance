<?php

namespace App\Http\Controllers\Api\V1\Receive;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Receive as V1Receive;
use App\Models\CasesUsers;
use App\Models\Profiles;
use App\Models\User;
use App\Traits\Api\V1\Responses;
use Illuminate\Http\Request;
use \Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class Receive extends Controller
{
    use Responses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $profile_receive = Profiles::with('receive')->paginate(10);
        return $this->success(status:Response::HTTP_OK , message:'Profile Retrieved Successfully.' , data:$profile_receive);
    }

    public function getAllDataWithoutPaginate(){
        $profile_receive = Profiles::with('receive')->get();
        return $this->success(status:Response::HTTP_OK , message:'Profile Retrieved Successfully.' , data:$profile_receive);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(V1Receive $request)
    {
        $pivotData = $request->only(['suggested_rate', 'description', 'status', 'estimation_time']);

        $user = auth()->guard('api')->user();
        $profile = $user->profile;

        if (!$profile) {
            return response()->json([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'User profile not found.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $case = \App\Models\Cases::find($request->caseId);
        if (!$case) {
            return response()->json([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Invalid case ID.',
            ], Response::HTTP_BAD_REQUEST);
        }

        $profile->receive()->attach($request->caseId, $pivotData);

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'User received the case successfully.',
            'data' => $profile->receive()->wherePivot('case_id', $request->caseId)->first(),
        ], Response::HTTP_OK);

        // return $this->success(status: Response::HTTP_OK, message: 'User received the case successfully.', data: $receive);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        $user_receive = CasesUsers::with('user' , 'user.receive' , 'cases' , 'cases.city')->where('id' , $id)->first();

        if (!$user_receive) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'received not found.',);
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
        $user_receive = DB::table('cases_users')->where('id' , $id)->where('user_id' , auth()->guard('api')->id())->first();

        if (!$user_receive) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'received not found.',);
        }

        $user_receive->delete();

        return $this->success(status: Response::HTTP_OK, message: 'User received Delete successfully.', data: $user_receive);

    }
}
