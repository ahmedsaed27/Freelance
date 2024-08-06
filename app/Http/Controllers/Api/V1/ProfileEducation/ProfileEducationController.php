<?php

namespace App\Http\Controllers\Api\V1\ProfileEducation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ProfileEducationRequest;
use App\Models\ProfileEducation;
use App\Traits\Api\V1\Responses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;


class ProfileEducationController extends Controller
{
    use Responses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = ProfileEducation::paginate(10);

        return $this->successPaginated(status: Response::HTTP_OK, message: 'ProfileEducation Retrieved Successfully.', data: $data);
    }

    public function getAllDataWithoutPaginate(){
        $data = ProfileEducation::get();

        return $this->success(status: Response::HTTP_OK, message: 'ProfileEducation Retrieved Successfully.', data: $data);
    }


    public function getLogs(string $id){
        $data = ProfileEducation::find($id);

        if(!$data){
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: "Sorry, the requested data was not found."
            );
        }

        $logs = Activity::where('subject_id', $data->id)
                        ->where('subject_type', ProfileEducation::class)
                        ->get();

        if ($logs->isEmpty()) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: "No logs found for the specified ProfileEducation."
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
    public function store(ProfileEducationRequest $request)
    {
        try {
            $user = auth()->guard('api')->user();
            $profile = $user->profile;

            if (!$profile) {
                return response()->json([
                    'message' => 'User does not have a profile'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            DB::beginTransaction();

            $request->merge([
                'profile_id' => $profile->id,
            ]);
            $profileEducations = ProfileEducation::create($request->except('certificate'));

            if ($request->hasFile('certificate')) {
                $profileEducations->addMediaFromRequest('certificate')
                ->withCustomProperties(['column' => 'certificate'])
                ->toMediaCollection('certificates', 'certificates');
            }


            DB::commit();

            return $this->success(status: Response::HTTP_OK, message: 'ProfileEducation Created Successfully.', data:$profileEducations);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: $e->getMessage());

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = ProfileEducation::find($id);

        if (!$data) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'ProfileEducation not found.',);
        }

        return $this->success(status: Response::HTTP_OK, message: 'ProfileEducation Retrieved Successfully.', data:$data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProfileEducationRequest $request, string $id)
    {
        try {
            $data = ProfileEducation::find($id);

            if (!$data) {
                return $this->error(status:Response::HTTP_INTERNAL_SERVER_ERROR , message:'ProfileEducation not found.');
            }

            $user = auth()->guard('api')->user();
            $profile = $user->profile;

            if(!$profile){
                return $this->error(status:Response::HTTP_INTERNAL_SERVER_ERROR , message:'User Dosnt Have Profile.');
            }

            if($data->profile_id != $profile->id){
                return $this->error(status:Response::HTTP_INTERNAL_SERVER_ERROR , message:'Only Profile Owner Can Update The Profile.');
            }

            DB::beginTransaction();

            $data->update($request->except('certificate'));

            if ($request->hasFile('certificate')) {
                $data->clearMediaCollection('certificates');
                
                $data->addMediaFromRequest('certificate')
                ->withCustomProperties(['column' => 'certificate'])
                ->toMediaCollection('certificates', 'certificates');
            }

            DB::commit();

            return $this->success(status: Response::HTTP_OK, message: 'ProfileEducation Updated Successfully.', data:$data);


        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error(status:Response::HTTP_INTERNAL_SERVER_ERROR , message:$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = ProfileEducation::find($id);

        if (!$data) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'ProfileEducation not found.',);
        }

        $data->delete();

        return $this->success(status: Response::HTTP_OK, message: 'ProfileEducation Deleted Successfully.', data: $data);
    }

     public function restore(string $id)
    {
        $data = ProfileEducation::withTrashed()->find($id);

        if (!$data) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: 'ProfileEducation not found.'
            );
        }

        if (!$data->trashed()) {

            return $this->error(
                status: Response::HTTP_BAD_REQUEST,
                message: 'ProfileEducation not found.'
            );
        }

        $data->restore();

        return $this->success(
            status:Response::HTTP_OK
            ,message: 'ProfileEducation restored successfully.'
            , data: $data
        );
    }

    public function getAllTrashedData(){
        $data = ProfileEducation::onlyTrashed()->paginate(10);

        return $this->successPaginated(
            status:Response::HTTP_OK
            , message:'ProfileEducation Retrived Succesfuly'
            , data: $data
        );
    }
}
