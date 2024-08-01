<?php

namespace App\Http\Controllers\Api\V1\ProfileSocials;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ProfileSocialsRequest;
use App\Http\Requests\Api\V1\UpdateProfileSocialsRequest;
use App\Models\ProfileSocials;
use App\Traits\Api\V1\Responses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;


class ProfileSocialsController extends Controller
{
    use Responses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = ProfileSocials::with('profile' , 'social')->paginate(10);

        return $this->successPaginated(status: Response::HTTP_OK, message: 'ProfileSocials Retrieved Successfully.', data: $data);
    }

    public function getAllDataWithoutPaginate(){
        $data = ProfileSocials::with('profile' , 'social')->get();

        return $this->success(status: Response::HTTP_OK, message: 'ProfileSocials Retrieved Successfully.', data: $data);
    }


    public function getLogs(string $id){
        $data = ProfileSocials::find($id);

        if(!$data){
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: "Sorry, the requested data was not found."
            );
        }

        $logs = Activity::where('subject_id', $data->id)
                        ->where('subject_type', ProfileSocials::class)
                        ->get();

        if ($logs->isEmpty()) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: "No logs found for the specified ProfileSocials."
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
    public function store(ProfileSocialsRequest $request)
    {
        try{
            $user = auth()->guard('api')->user();
            $profile = $user->profile;

            if(!$profile){
                return $this->error(status:Response::HTTP_INTERNAL_SERVER_ERROR , message:'User Dosnt Have Profile');
            }

            DB::beginTransaction();

            $socials = $request->validated()['socials'];

            foreach ($socials as $social) {
                ProfileSocials::create([
                    'profile_id' => $profile->id,
                    'social_id' => $social['social_id'],
                    'link' => $social['link'],
                ]);
            }
            DB::commit();

            return $this->success(status: Response::HTTP_OK, message: 'ProfileSocials Retrieved Successfully.', data: $socials);
        }catch(Exception $e){
            return $this->error(status:Response::HTTP_INTERNAL_SERVER_ERROR , message:$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = ProfileSocials::with('profile' , 'social')->find($id);

        if (!$data) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'ProfileSocials not found.',);
        }

        return $this->success(status: Response::HTTP_OK, message: 'ProfileSocials Retrieved Successfully.', data:$data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProfileSocialsRequest $request, string $id)
    {
        $data = ProfileSocials::find($id);
        $user = auth()->guard('api')->user();
        $profile = $user->profile;


        if (!$data) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'ProfileSocials not found.',);
        }

        if(!$profile){
            return $this->error(status:Response::HTTP_INTERNAL_SERVER_ERROR , message:'User Dosnt Have Profile');
        }

        if($data->profile_id !=  $profile->id){
            return $this->error(status:Response::HTTP_INTERNAL_SERVER_ERROR , message:'Only Profile Owner CAn Update Socials');
        }

        DB::beginTransaction();

        $data->update($request->validated());

        DB::commit();


        return $this->success(status: Response::HTTP_OK, message: 'ProfileSocials Updated Successfully.', data:$data);
    }

    public function updateProfileSocialsArrayByProfileId(ProfileSocialsRequest $request){

        try{
            $user = auth()->guard('api')->user();
            $profile = $user->profile;


            if(!$profile){
                return $this->error(status:Response::HTTP_INTERNAL_SERVER_ERROR , message:'User Dosnt Have Profile');
            }


            $socials = $request->validated()['socials'];

            foreach ($socials as $q) {
                $pivotSocials = $profile->socials()->wherePivot('social_id', $q['social_id'])->get();

                // Loop through each social media and update
                foreach ($pivotSocials as $pivotSocial) {
                    $pivotSocial->pivot->update([
                        'social_id' => $q['social_id'],
                        'link' => $q['link'],
                    ]);
                }
            }


            return $this->success(status: Response::HTTP_OK, message: 'ProfileSocials Updated Successfully.', data:$socials);
        }catch(Exception $e){
            return $this->error(status:Response::HTTP_INTERNAL_SERVER_ERROR , message:$e->getMessage());
        }


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = ProfileSocials::find($id);

        if (!$data) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'ProfileSocials not found.',);
        }

        $data->delete();

        return $this->success(status: Response::HTTP_OK, message: 'ProfileSocials Deleted Successfully.', data: $data);
    }

     public function restore(string $id)
    {
        $data = ProfileSocials::withTrashed()->find($id);

        if (!$data) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: 'ProfileSocials not found.'
            );
        }

        if (!$data->trashed()) {

            return $this->error(
                status: Response::HTTP_BAD_REQUEST,
                message: 'ProfileSocials not found.'
            );
        }

        $data->restore();

        return $this->success(
            status:Response::HTTP_OK
            ,message: 'ProfileSocials restored successfully.'
            , data: $data
        );
    }

    public function getAllTrashedData(){
        $data = ProfileSocials::onlyTrashed()->paginate(10);

        return $this->successPaginated(
            status:Response::HTTP_OK
            , message:'ProfileSocials Retrived Succesfuly'
            , data: $data
        );
    }
}
