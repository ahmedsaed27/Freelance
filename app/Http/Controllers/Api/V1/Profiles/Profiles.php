<?php

namespace App\Http\Controllers\Api\V1\Profiles;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Profiles as ProfilesRequest;
use App\Models\Profiles as ProfilesModel;
use App\Traits\Api\V1\Responses;
use Exception;
use \Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Profiles extends Controller
{
    use Responses;

    public function __construct()
    {
        $this->middleware('userProfile')->only('store');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 'socials', 'workExperiences', 'education'
        $profile = ProfilesModel::with('user' , 'profileType' , 'currency' , 'country' , 'city')->paginate(10);

        return $this->success(status: Response::HTTP_OK, message: 'Profiles Retrieved Successfully.', data: $profile);
    }


    public function getProfileFromToken(){
        $profile = ProfilesModel::with('user' , 'profileType' , 'currency' , 'country' , 'city' , 'socials', 'workExperiences', 'education')
        ->where('user_id' , auth()->guard('api')->id())
        ->first();

        if(!$profile){
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message:'User Dosnt Have Profile');
        }

        return $this->success(status: Response::HTTP_OK, message: 'Profile Retrieved Successfully.', data: $profile);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProfilesRequest $request)
    {
        try {

            DB::beginTransaction();

            $profile = ProfilesModel::create([
                'user_id' => auth()->guard('api')->id(),
                'address' => $request->address,
                'areas_of_expertise' => $request->areas_of_expertise,
                'hourly_rate' => $request->hourly_rate,
                'years_of_experience' => $request->years_of_experience,
                'type' => $request->type,
                'career' => $request->career,
                'country_id' => $request->country_id,
                'city_id' => $request->city_id,
                'currency_id' => $request->currency_id,
                'specialization' => $request->specialization,
                'level' => $request->level,
                'field' => $request->field,
                'status' => 'Under Review', // Automatically set status to 'Under Review' for POST requests
            ]);

            $profile->profileType()->sync($request->input('types'));

            $profile->addMediaFromRequest('image')
            ->withCustomProperties(['column' => 'image'])
            ->toMediaCollection('profiles', 'profiles');

            $profile->addMediaFromRequest('cv')
            ->withCustomProperties(['column' => 'cv'])
            ->toMediaCollection('profiles', 'profiles');

            DB::commit();

            return $this->success(status: Response::HTTP_OK, message: 'Profiles Created Successfully.', data: [
                'profile' => $profile,
            ]);
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
        $profile = ProfilesModel::where('id' , $id)->first();

        if (!$profile) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Profile not found.',);
        }

        $profile->load('user' , 'user', 'socials', 'workExperiences', 'education' , 'profileType' , 'currency' , 'country' , 'city');
        $profile->getMedia('profiles');

        return $this->success(status: Response::HTTP_OK, message: 'Profiles Retrieved Successfully.', data: [
            'profile' => $profile,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProfilesRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Fetch the existing profile with related models
            $profile = ProfilesModel::with(['education', 'workExperiences', 'socials'])
                ->where('user_id' , auth()->guard('api')->id())
                ->where('id', $id)
                ->first();

            if (!$profile) {
                return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Profile not found.');
            }

            // Update profile attributes
            $profile->update($request->only([
                'address', 'areas_of_expertise', 'hourly_rate', 'years_of_experience', 'currency_id',
                'type', 'career', 'country_id', 'city_id', 'specialization', 'level' , 'status'
            ]));

            $profile->profileType()->sync($request->input('types'));

            // Update media
            $this->updateMedia($profile, $request);

            $profile->load('media');

            DB::commit();

            return $this->success(status: Response::HTTP_OK, message: 'Profile Updated Successfully.', data: ['profile' => $profile]);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: $e->getMessage());
        }
    }


    public function updateProfileFromToken(ProfilesRequest $request){
        try {
            DB::beginTransaction();
            
            // Fetch the existing profile with related models
            $profile = ProfilesModel::where('user_id' , auth()->guard('api')->id())->first();

            if (!$profile) {
                return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Profile not found.');
            }

            dd($profile);

            // Update profile attributes
            $profile->update($request->only([
                'address', 'areas_of_expertise', 'hourly_rate', 'years_of_experience', 'currency_id',
                'type', 'career', 'country_id', 'city_id', 'specialization', 'level' , 'status'
            ]));

            $profile->profileType()->sync($request->input('types'));

            // Update media
            $this->updateMedia($profile, $request);

            $profile->load('media');

            DB::commit();

            return $this->success(status: Response::HTTP_OK, message: 'Profile Updated Successfully.', data: ['profile' => $profile]);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: $e->getMessage());
        }
    }

    private function updateMedia($profile, $request)
    {
        $existingMedia = $profile->getMedia('profiles');

        if ($request->hasFile('image')) {
            $existingImageMedia = $existingMedia->filter(function ($media) {
                return $media->getCustomProperty('column') === 'image';
            });

            if ($existingImageMedia->isNotEmpty()) {
                $existingImageMedia->each->delete();
            }

            $profile->addMediaFromRequest('image')
            ->withCustomProperties(['column' => 'image'])
            ->toMediaCollection('profiles', 'profiles');
        }

        if ($request->hasFile('cv')) {
            $existingImageMedia = $existingMedia->filter(function ($media) {
                return $media->getCustomProperty('column') === 'cv';
            });

            if ($existingImageMedia->isNotEmpty()) {
                $existingImageMedia->each->delete();
            }

            $profile->addMediaFromRequest('cv')
            ->withCustomProperties(['column' => 'cv'])
            ->toMediaCollection('profiles', 'profiles');
        }
    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Fetch the existing profile with related models
            $profile = ProfilesModel::with(['education', 'workExperiences', 'socials'])
                ->where('user_id', auth()->guard('api')->id())
                ->where('id', $id)
                ->first();

            if (!$profile) {
                return $this->error(status: Response::HTTP_NOT_FOUND, message: 'Profile not found.');
            }

            // Delete media associated with the profile
            $profile->clearMediaCollection('profiles');

            // Delete education media and records

            if($profile->education){
                foreach ($profile->education as $education) {
                    $education->clearMediaCollection('certificates');
                    $education->delete();
                }
            }

            // Delete work experience media and records

            if($profile->workExperiences){
                foreach ($profile->workExperiences as $workExperience) {
                    $workExperience->clearMediaCollection('certificates');
                    $workExperience->delete();
                }
            }


            // Delete socials if necessary
            if ($profile->socials) {
                foreach($profile->socials as $social){
                    $social->delete();
                }
            }


            if($profile->profileType){
                DB::table('profile_type')
                ->where('profile_id', $profile->id)
                ->update(['profile_type.deleted_at' => now()]);
            }

            // Finally, delete the profile
            $profile->delete();

            DB::commit();

            return $this->success(status: Response::HTTP_OK, message: 'Profile deleted successfully.', data:$profile);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: $e->getMessage());
        }
    }


    public function userHaveProfile(){

        if(ProfilesModel::where('user_id' , auth()->guard('api')->id())->exists()){
            return Response()->json([
                'message' => 'User Alredy Have Profile',
                'status' => true,
            ], Response::HTTP_OK);
        }


        return Response()->json([
            'message' => 'User doesnt Have Profile',
            'status' => false,
        ], Response::HTTP_OK);

    }


    public function restore(string $id)
    {
        $data = ProfilesModel::withTrashed()->find($id);

        if (!$data) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: 'Type not found.'
            );
        }

        if (!$data->trashed()) {

            return $this->error(
                status: Response::HTTP_BAD_REQUEST,
                message: 'Type not found.'
            );
        }

        $data->restore();
        $data->education()->withTrashed()->restore();
        $data->workExperiences()->withTrashed()->restore();
        $data->socials()->withTrashed()->restore();

        DB::table('profile_type')
        ->where('profile_id', $data->id)
        ->update(['profile_type.deleted_at' => null]);


        return $this->success(
            status:Response::HTTP_OK
            ,message: 'Type restored successfully.'
            , data: $data
        );
    }

    public function getAllTrashedData(){
        $data = ProfilesModel::onlyTrashed()->paginate(10);

        return $this->successPaginated(
            status:Response::HTTP_OK
            , message:'Type Retrived Succesfuly'
            , data: $data
        );
    }

    public function getUserProfile(){
        $profile = auth()->guard('api')->user()?->profile;

        if(!$profile){
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'User Dosnt Have Any Profile.',);
        }

        $profile->load('user', 'socials', 'workExperiences', 'education' , 'profileType' , 'currency' , 'country' , 'city');

        return $this->success(status: Response::HTTP_OK, message: 'Profile received successfully.', data: $profile);

    }
}
