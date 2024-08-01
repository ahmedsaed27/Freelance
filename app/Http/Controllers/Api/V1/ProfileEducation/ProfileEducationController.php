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

            $educations = $request->validated()['education'];

            foreach ($educations as $index => $education) {
                $profileEducations = ProfileEducation::create([
                    'profile_id' => $profile->id,
                    'major' => $education['major'],
                    'grade' => $education['grade'],
                    'degree' => $education['degree'],
                    'qualification' => $education['qualification'] ?? null,
                    'university' => $education['university'],
                    'country_id' => $education['country_id'],
                    'additional_information' => $education['additional_information'] ?? null,
                    'start_date' => $education['start_date'],
                    'end_date' => $education['end_date'],
                ]);

                // If there is a certificate in the request, add it to the media collection
                if ($request->hasFile("education.$index.certificate")) {
                    $profileEducations->addMediaFromRequest("education.$index.certificate")->toMediaCollection('certificates', 'certificates');
                }
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
                return response()->json([
                    'message' => 'ProfileEducation not found.'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
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

            $educationData = $request->validated()['education'][0]; // Assuming you're updating a single education record

            $data->update([
                'major' => $educationData['major'],
                'grade' => $educationData['grade'],
                'degree' => $educationData['degree'],
                'qualification' => $educationData['qualification'] ?? null,
                'university' => $educationData['university'],
                'country_id' => $educationData['country_id'],
                'additional_information' => $educationData['additional_information'] ?? null,
                'start_date' => $educationData['start_date'],
                'end_date' => $educationData['end_date'],
            ]);

            // If there is a certificate in the request, add or update it in the media collection
            if ($request->hasFile("education.0.certificate")) {
                $data->clearMediaCollection('certificates'); // Clear existing certificates
                $data->addMediaFromRequest("education.0.certificate")->toMediaCollection('certificates', 'certificates');
            }

            DB::commit();

            return response()->json([
                'message' => 'ProfileEducation Updated Successfully.',
                'data' => $data
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update ProfileEducation',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
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
