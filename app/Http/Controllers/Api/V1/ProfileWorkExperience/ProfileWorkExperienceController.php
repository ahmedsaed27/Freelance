<?php

namespace App\Http\Controllers\Api\V1\ProfileWorkExperience;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ProfileWorkExperienceRequest;
use App\Models\ProfileWorkExperience;
use App\Traits\Api\V1\Responses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;


class ProfileWorkExperienceController extends Controller
{
    use Responses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = ProfileWorkExperience::paginate(10);

        return $this->successPaginated(status: Response::HTTP_OK, message: 'ProfileWorkExperience Retrieved Successfully.', data: $data);
    }

    public function getAllDataWithoutPaginate(){
        $data = ProfileWorkExperience::get();

        return $this->success(status: Response::HTTP_OK, message: 'ProfileWorkExperience Retrieved Successfully.', data: $data);
    }


    public function getLogs(string $id){
        $data = ProfileWorkExperience::find($id);

        if(!$data){
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: "Sorry, the requested data was not found."
            );
        }

        $logs = Activity::where('subject_id', $data->id)
                        ->where('subject_type', ProfileWorkExperience::class)
                        ->get();

        if ($logs->isEmpty()) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: "No logs found for the specified ProfileWorkExperience."
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
    public function store(ProfileWorkExperienceRequest $request)
    {
        try {
            $user = auth()->guard('api')->user();
            $profile = $user->profile;

            if (!$profile) {
                return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'User Dosnt Have Profile.',);
            }

            DB::beginTransaction();

            $request->merge([
                'profile_id' => $profile->id,
            ]);

            $profileWorkExperience = ProfileWorkExperience::create($request->except('certificate'));

            if ($request->hasFile('certificate')) {
                $profileWorkExperience->addMediaFromRequest('certificate')->toMediaCollection('certificates', 'certificates');
            }

            DB::commit();

            return $this->success(status: Response::HTTP_OK, message: 'ProfileWorkExperience Created Successfully.', data:$profileWorkExperience);


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
        $data = ProfileWorkExperience::find($id);

        if (!$data) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'ProfileWorkExperience not found.',);
        }

        return $this->success(status: Response::HTTP_OK, message: 'ProfileWorkExperience Retrieved Successfully.', data:$data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProfileWorkExperienceRequest $request, string $id)
    {
        try {
            $data = ProfileWorkExperience::find($id);

            if (!$data) {
                return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'ProfileWorkExperience not found.',);
            }

            $user = auth()->guard('api')->user();
            $profile = $user->profile;

            if (!$profile) {
                return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'User Dosnt Have Profile.',);
            }

            if($profile->id != $data->profile_id){
                return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Only Profile Owner Can Update The Profile.',);
            }
            DB::beginTransaction();

            $request->merge([
                'profile_id' => $profile->id,
            ]);

            $data->update($request->except('certificate'));

            if ($request->hasFile('certificate')) {
                $data->clearMediaCollection('certificates');
                $data->addMediaFromRequest('certificate')->toMediaCollection('certificates', 'certificates');
            }

            DB::commit();

            return $this->success(status: Response::HTTP_OK, message: 'ProfileWorkExperience Updated Successfully.', data: $data);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: $e->getMessage());

        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = ProfileWorkExperience::find($id);

        if (!$data) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'ProfileWorkExperience not found.',);
        }

        $data->delete();

        return $this->success(status: Response::HTTP_OK, message: 'ProfileWorkExperience Deleted Successfully.', data: $data);
    }

     public function restore(string $id)
    {
        $data = ProfileWorkExperience::withTrashed()->find($id);

        if (!$data) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: 'ProfileWorkExperience not found.'
            );
        }

        if (!$data->trashed()) {

            return $this->error(
                status: Response::HTTP_BAD_REQUEST,
                message: 'ProfileWorkExperience not found.'
            );
        }

        $data->restore();

        return $this->success(
            status:Response::HTTP_OK
            ,message: 'ProfileWorkExperience restored successfully.'
            , data: $data
        );
    }

    public function getAllTrashedData(){
        $data = ProfileWorkExperience::onlyTrashed()->paginate(10);

        return $this->successPaginated(
            status:Response::HTTP_OK
            , message:'ProfileWorkExperience Retrived Succesfuly'
            , data: $data
        );
    }
}
