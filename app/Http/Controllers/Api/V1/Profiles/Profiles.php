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
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $profile = ProfilesModel::with('user', 'socials', 'workExperiences', 'education')->paginate(10);

        return $this->success(status: Response::HTTP_OK, message: 'Profiles Retrieved Successfully.', data: $profile);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProfilesRequest $request)
    // public function store(Request $request)
    {

        try {

            DB::beginTransaction();

            $profile = ProfilesModel::create([
                'user_id' => auth()->guard('api')->id(),
                'location' => $request->location,
                'areas_of_expertise' => $request->areas_of_expertise,
                'hourly_rate' => $request->hourly_rate,
                'years_of_experience' => $request->years_of_experience,
                'type' => $request->type,
                'career' => $request->career,
                'countries_id' => $request->countries_id,
                'cities_id' => $request->cities_id,
                'specialization' => $request->specialization,
                'experience' => $request->experience,
            ]);

            $profile->addMediaFromRequest('image')->toMediaCollection('profiles', 'profiles');
            $profile->addMediaFromRequest('union_card')->toMediaCollection('profiles', 'profiles');
            $profile->addMediaFromRequest('cv')->toMediaCollection('profiles', 'profiles');


            $profile->socials()->create($request->socials);

            collect($request->education)->map(function ($data, $index) use ($profile) {
                $education = $profile->education()->create([
                    'qualification' => $data['qualification'],
                    'university' => $data['university'],
                    'specialization' => $data['specialization'],
                    'countries_id' => $data['countries_id'],
                    'additional_information' => $data['additional_information'],
                ]);

                $education->addMediaFromRequest("education.$index.certificate")->toMediaCollection('certificates', 'certificates');
            });


            collect($request->work_experience)->map(function ($data, $index) use ($profile) {
                $education = $profile->workExperiences()->create([
                    'job_name' => $data['job_name'],
                    'countries_id' => $data['countries_id'],
                    'section' => $data['section'],
                    'specialization' => $data['specialization'],
                    'job_type' => $data['job_type'],
                    'work_place' => $data['work_place'],
                    'responsibilities' => $data['responsibilities'],
                    'career_level' => $data['career_level'],
                    'from' => $data['from'],
                    'to' => $data['to'],
                ]);

                $education->addMediaFromRequest("work_experience.$index.certificate")->toMediaCollection('certificates', 'certificates');
            });


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
        $profile = ProfilesModel::where('user_id', auth()->guard('api')->id())->where('id' , $id)->first();

        if (!$profile) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Profile not found.',);
        }

        $profile->load('user' , 'user', 'socials', 'workExperiences', 'education');
        $profile->getMedia('profiles');

        return $this->success(status: Response::HTTP_OK, message: 'Profiles Retrieved Successfully.', data: [
            'profile' => $profile,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProfilesRequest $request, string $id)
    {
        $profile = ProfilesModel::where('user_id', auth()->guard('api')->id())->first();

        if (!$profile) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Profile not found.',);
        }

        $profile->update($request->except('image', 'cv'));


        $profile->clearMediaCollection('profiles');
        $profile->addMediaFromRequest('image')->toMediaCollection('profiles', 'profiles');
        $profile->addMediaFromRequest('cv')->toMediaCollection('profiles', 'profiles');


        return $this->success(status: Response::HTTP_OK, message: 'Profiles Updated Successfully.', data: [
            'profile' => $profile,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $profile = ProfilesModel::where('user_id', auth()->guard('api')->id())->first();

        $profile->clearMediaCollection('profiles');

        $profile->delete();

        return $this->success(status: Response::HTTP_OK, message: 'Profiles Deleted Successfully.', data: []);
    }
}
