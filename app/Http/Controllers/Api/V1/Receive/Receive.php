<?php

namespace App\Http\Controllers\Api\V1\Receive;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Receive as V1Receive;
use App\Models\CasesProfile;
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
        $profile_receive = CasesProfile::with('profile'  , 'profile.user', 'cases' , 'cases.city' , 'currency')->paginate(10);
        return $this->successPaginated(status:Response::HTTP_OK , message:'Profile Retrieved Successfully.' , data:$profile_receive);
    }

    public function getAllDataWithoutPaginate(){
        $profile_receive = CasesProfile::with('profile'  , 'profile.user', 'cases' , 'cases.city' , 'currency')->get();
        return $this->success(status:Response::HTTP_OK , message:'Profile Retrieved Successfully.' , data:$profile_receive);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(V1Receive $request)
    {
        $pivotData = $request->only(['suggested_rate', 'estimation_time' , 'description']);
        $pivotData['status'] = 'Pending';

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

        $pivotData['currency_id'] = $case->currency_id;

        $profile->receive()->attach($request->caseId, $pivotData);

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'User received the case successfully.',
            'data' => CasesProfile::where('case_id' , $case->id)->where('profile_id' ,  $profile->id)->first(),
        ], Response::HTTP_OK);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        $profile_receive = CasesProfile::with('profile'  , 'profile.user', 'cases' , 'cases.city' , 'currency')->find($id);

        if (!$profile_receive) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'received not found.',);
        }

        return $this->success(status: Response::HTTP_OK, message: 'Profile received the case successfully.', data: $profile_receive);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(V1Receive $request, string $id)
    {
        $data = CasesProfile::find($id);

        if(!$data){
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'received not found.',);
        }

        $profile = auth()->guard('api')->user()?->profile;

        if(!$profile){
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'The User Dosnt Have Any Profile.',);
        }

        if($data->profile_id != $profile->id){
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Only Profile Owner Can Be Update Data.',);
        }

        $data->update($request->except('status'));

        return $this->success(status: Response::HTTP_OK, message: 'received the case Updated successfully.', data: $data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = CasesProfile::find($id);

        if (!$data) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'received not found.',);
        }

        $profile = auth()->guard('api')->user()?->profile;

        if(!$profile){
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'The User Dosnt Have Any Profile.',);
        }

        if($data->profile_id != $profile->id){
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Only Profile Owner Can Be Delete Data.',);
        }

        $data->delete();

        return $this->success(status: Response::HTTP_OK, message: 'User received Delete successfully.', data: $data);

    }


    public function restore(string $id)
    {
        $data = CasesProfile::withTrashed()->find($id);

        if (!$data) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: 'Case Profile not found.'
            );
        }

        if (!$data->trashed()) {

            return $this->error(
                status: Response::HTTP_BAD_REQUEST,
                message: 'Case Profile not found.'
            );
        }

        $data->restore();

        return $this->success(
            status:Response::HTTP_OK
            ,message: 'Case Profile restored successfully.'
            , data: $data
        );
    }

    public function getAllTrashedData(){
        $data = CasesProfile::onlyTrashed()->paginate(10);

        return $this->successPaginated(
            status:Response::HTTP_OK
            , message:'Case Profile Retrived Succesfuly'
            , data: $data
        );
    }


}
