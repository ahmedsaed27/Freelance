<?php

namespace App\Http\Controllers\Api\V1\Verification;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\VerificatioRequest;
use App\Models\Verification as VerificationModel;
use App\Traits\Api\V1\Responses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class Verification extends Controller
{
    use Responses;
    /**
     * Display a listing of the resource.
     */

     public function __construct()
    {
        $this->middleware('verificationProfile')->only('store');
    }

    public function index()
    {
        $verification = VerificationModel::with('profile')->paginate(10);

        return $this->successPaginated(status:Response::HTTP_OK , message:'verifications Retrieved Successfully.' , data:$verification);
    }

    public function getAllDataWithoutPaginate(){
        $data = VerificationModel::with('profile')->get();

        return $this->success(status: Response::HTTP_OK, message: 'verifications Retrieved Successfully.', data: $data);
    }

    public function getLogs(string $id){
        $data = VerificationModel::find($id);

        if(!$data){
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: "Sorry, the requested data was not found."
            );
        }

        $logs = Activity::where('subject_id', $data->id)
                        ->where('subject_type', VerificationModel::class)
                        ->get();

        if ($logs->isEmpty()) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: "No logs found for the specified Verification."
            );
        }


        return $this->success(
            status:Response::HTTP_OK
            , message:'Logs Retrived Succesfuly'
            , data:  $logs
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VerificatioRequest $request)
    {
        try {
            $user = auth()->guard('api')->user();
            $profile = $user->profile;

            if (!$profile) {
                return $this->error(status: 500, message: 'User doesn\'t have a profile.');
            }

            $request->merge(['profile_id' => $profile->id]);

            DB::beginTransaction();

            $verification = VerificationModel::create($request->all());

            DB::commit();

            return $this->success(status: Response::HTTP_OK, message: 'Verification created successfully.', data: $verification);

        } catch (Exception $e) {
            DB::rollBack();

            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: $e->getMessage());
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $verifications = VerificationModel::with('profile')->find($id);

        if (!$verifications) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'verifications not found.',);
        }

        return $this->success(status: Response::HTTP_OK, message: 'verifications Retrieved Successfully.', data: [
            'verifications' => $verifications,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VerificatioRequest $request, string $id)
    {
        try {
            $verification = VerificationModel::find($id);

            if (!$verification) {
                return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Verification not found.');
            }

            $user = auth()->guard('api')->user();
            $profile = $user->profile;

            if (!$profile) {
                return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'User doesn\'t have a profile.');
            }

            if ($verification->profile_id != $profile->id) {
                return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Only the profile owner can update the profile.');
            }

            $request->merge(['profile_id' => $profile->id]);

            $verification->update($request->all());

            return $this->success(status: Response::HTTP_OK, message: 'Verification updated successfully.', data: ['profile' => $verification]);
        } catch (Exception $e) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $verification = VerificationModel::find($id);


        if (!$verification) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'verifications not found.',);
        }

        $user = auth()->guard('api');
        $profile = $user->user()?->profile;

        if(!$profile){
           $this->error(status:500 , message:'User Dosnt Have Profile.');
        }


        if($verification->profile_id != $profile->id){
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Only Profile Owner Can Delete The Profile.',);
        }

        $verification->delete();

        return $this->success(status: Response::HTTP_OK, message: 'verification Deleted Successfully.',data:$verification);
    }


    public function restore(string $id)
    {
        $data = VerificationModel::withTrashed()->find($id);

        if (!$data) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: 'Verification not found.'
            );
        }

        if (!$data->trashed()) {

            return $this->error(
                status: Response::HTTP_BAD_REQUEST,
                message: 'Verification not found.'
            );
        }

        $data->restore();

        return $this->success(
            status:Response::HTTP_OK
            ,message: 'Type restored successfully.'
            , data: $data
        );
    }

    public function getAllTrashedData(){
        $data = VerificationModel::onlyTrashed()->paginate(10);

        return $this->successPaginated(
            status:Response::HTTP_OK
            , message:'Verification Retrived Succesfuly'
            , data: $data
        );
    }
}
