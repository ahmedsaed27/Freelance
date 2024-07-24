<?php

namespace App\Http\Controllers\Api\V1\ProfilePaper;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\GetProfilePaperByIDRequest;
use App\Http\Requests\Api\V1\ProfilePaperRequest;
use App\Http\Requests\Api\V1\UpdateProfilePaperStatusRequest;
use App\Http\Resources\ProfilePaperResource;
use App\Models\Paper;
use App\Models\ProfilePaper as ProfilePaperModel;
use App\Traits\Api\V1\Responses as V1Responses;
use App\Traits\Api\V1\UploadFilesTrait as V1UploadFilesTrait;
use Illuminate\Http\Response;

class ProfilePaper extends Controller
{
    use V1Responses, V1UploadFilesTrait;

    public function index(GetProfilePaperByIDRequest $request)
    {
        $data = $request->all();

        $profile_paper = ProfilePaperModel::where('profiles_id', $data['profiles_id'])->orderBy('id', 'DESC')->with(['profile', 'papers'])->paginate(10);

        return $this->successPaginated(data: ProfilePaperResource::collection($profile_paper), status: Response::HTTP_OK, message: 'All Profile Papers.');
    }

    public function createProfilePapers(ProfilePaperRequest $request)
    {
        $data = $request->all();
        $paper = Paper::find($data['papers_id']);

        if ($paper->data_type == 'file' && isset($data['value'])) {
            $file_new_name = $data['value']->hashName();
            $data['value']->move($this->createDirectory("profile/{$request->input('corporate_id')}/papers"), $file_new_name);
            $data['value'] = "profile/{$request->input('corporate_id')}/papers/" . $file_new_name;
        }

        $profile_paper = ProfilePaperModel::create($data);

        return $this->success(status: Response::HTTP_OK, message: 'Profile Paper created successfully', data: new ProfilePaperResource($profile_paper));
    }

    public function updateProfilePaperstStatus(UpdateProfilePaperStatusRequest $request)
    {
        $data = $request->all();

        $profile_paper = ProfilePaperModel::where('id', $data['profile_paper_id'])->first();

        $profile_paper->update(['status' => $data['status']]);

        $profile_paper->load('profile', 'papers');

        return $this->success(status: Response::HTTP_OK, message: 'Profile Paper Status updated successfully', data: new ProfilePaperResource($profile_paper));
    }

    public function getProfilePapersById(GetProfilePaperByIDRequest $request)
    {
        $data = $request->all();
        $profile_paper = ProfilePaperModel::where('id', $data['profile_paper_id'])->with(['profile', 'papers'])->first();

        return $this->success(status: Response::HTTP_OK, message: 'Profile Paper Details successfully', data: new ProfilePaperResource($profile_paper));
    }

    public function deleteProfilePapers(GetProfilePaperByIDRequest $request)
    {
        $data = $request->all();
        $profile_paper = ProfilePaperModel::where('id', $data['profile_paper_id'])->first();
        $paper = Paper::findOrFail($profile_paper->papers_id);

        if ($paper->data_type == 'file' && $profile_paper->value) {
            $this->deleteFile($profile_paper->value);
        }

        $profile_paper->delete();

        return $this->success(status: Response::HTTP_OK, message: 'Profile Paper Deleted successfully', data: new ProfilePaperResource($profile_paper));
    }
}
