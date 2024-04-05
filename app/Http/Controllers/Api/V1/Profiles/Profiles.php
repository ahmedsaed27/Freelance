<?php

namespace App\Http\Controllers\Api\V1\Profiles;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Profiles as ProfilesRequest;
use App\Models\Profiles as ProfilesModel;
use App\Traits\Api\V1\Responses;
use \Illuminate\Http\Response;



class Profiles extends Controller
{
    use Responses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $profile = ProfilesModel::with('media')->paginate(10);

        return $this->success(status: Response::HTTP_OK, message: 'Profiles Retrieved Successfully.', data: $profile);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProfilesRequest $request)
    {
        $profile = ProfilesModel::create([
            'user_id' => auth()->guard('api')->id(),
            'location' => $request->location,
            'areas_of_expertise' => $request->areas_of_expertise,
            'hourly_rate' => $request->hourly_rate,
            'years_of_experience' => $request->years_of_experience,
        ]);

        $profile->addMediaFromRequest('image')->toMediaCollection('profiles', 'profiles');


        $profile->addMediaFromRequest('cv')->toMediaCollection('profiles', 'profiles');

        $profile->getMedia('profiles');

        return $this->success(status: Response::HTTP_OK, message: 'Profiles Created Successfully.', data: [
            'profile' => $profile,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $profile = ProfilesModel::where('user_id' , auth()->guard('api')->id())->first();

        if (!$profile) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Profile not found.',);
        }

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
        $profile = ProfilesModel::where('user_id' , auth()->guard('api')->id())->first();

        if (!$profile) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Profile not found.',);
        }

        $profile->update($request->except('image', 'cv'));


        $profile->clearMediaCollection('profiles');
        $profile->addMediaFromRequest('image')->toMediaCollection('profiles', 'profiles');
        $profile->addMediaFromRequest('cv')->toMediaCollection('profiles', 'profiles');


        $profile->getMedia('profiles');

        return $this->success(status: Response::HTTP_OK, message: 'Profiles Updated Successfully.', data: [
            'profile' => $profile,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $profile = ProfilesModel::where('user_id' , auth()->guard('api')->id())->first();

        $profile->clearMediaCollection('profiles');

        $profile->delete();

        return $this->success(status: Response::HTTP_OK, message: 'Profiles Deleted Successfully.',data:[]);
    }
}
