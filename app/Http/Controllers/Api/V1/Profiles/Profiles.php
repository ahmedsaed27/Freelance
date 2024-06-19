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
        $profile = ProfilesModel::with('user', 'socials', 'workExperiences', 'education')->paginate(10);

        return $this->success(status: Response::HTTP_OK, message: 'Profiles Retrieved Successfully.', data: $profile);
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
        $profile = ProfilesModel::where('id' , $id)->first();

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
    public function update(ProfilesRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Fetch the existing profile with related models
            $profile = ProfilesModel::with(['education', 'workExperiences', 'socials'])
                ->where('id', $id)
                ->first();

            if (!$profile) {
                return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Profile not found.');
            }

            // Update profile attributes
            $profile->update($request->only([
                'location', 'areas_of_expertise', 'hourly_rate', 'years_of_experience',
                'type', 'career', 'countries_id', 'cities_id', 'specialization', 'experience'
            ]));

            // Update media
            $this->updateMedia($profile, $request);

            // Update socials
            if ($profile->socials) {
                $profile->socials()->update($request->socials);
            }

            // Batch update education
            $this->updateRelatedRecords($profile->education, $request->education, 'education', 'qualification', 'certificates');

            // Batch update work experiences
            $this->updateRelatedRecords($profile->workExperiences, $request->work_experience, 'work_experience', 'job_name', 'certificates');

            DB::commit();

            return $this->success(status: Response::HTTP_OK, message: 'Profile Updated Successfully.', data: ['profile' => $profile]);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: $e->getMessage());
        }
    }

    private function updateMedia($profile, $request)
    {
        $mediaFiles = ['image', 'union_card', 'cv'];
        $mediaUpdated = false;

        foreach ($mediaFiles as $file) {
            if ($request->hasFile($file)) {
                $mediaUpdated = true;
                break;
            }
        }

        if ($mediaUpdated) {
            $profile->clearMediaCollection('profiles');
            foreach ($mediaFiles as $file) {
                if ($request->hasFile($file)) {
                    $profile->addMediaFromRequest($file)->toMediaCollection('profiles', 'profiles');
                }
            }
        }
    }

    private function updateRelatedRecords($existingRecords, $newRecords, $requestKey, $uniqueKey, $mediaCollection)
    {
        $newRecordsCollection = collect($newRecords);
        foreach ($existingRecords as $existingRecord) {
            $data = $newRecordsCollection->firstWhere($uniqueKey, $existingRecord->$uniqueKey);

            if ($data) {
                $existingRecord->update($data);

                $index = $newRecordsCollection->search(fn ($item) => $item[$uniqueKey] === $existingRecord->$uniqueKey);
                if (request()->hasFile("$requestKey.$index.certificate")) {
                    $existingRecord->clearMediaCollection($mediaCollection);
                    $existingRecord->addMediaFromRequest("$requestKey.$index.certificate")->toMediaCollection($mediaCollection, $mediaCollection);
                }
            }
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
            foreach ($profile->education as $education) {
                $education->clearMediaCollection('certificates');
                $education->delete();
            }

            // Delete work experience media and records
            foreach ($profile->workExperiences as $workExperience) {
                $workExperience->clearMediaCollection('certificates');
                $workExperience->delete();
            }

            // Delete socials if necessary
            if ($profile->socials) {
                $profile->socials()->delete();
            }

            // Finally, delete the profile
            $profile->delete();

            DB::commit();

            return $this->success(status: Response::HTTP_OK, message: 'Profile deleted successfully.', data:[]);
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

}
