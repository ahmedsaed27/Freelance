<?php

namespace App\Http\Controllers\Api\V1\Papers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\GetPaperByIdRequest;
use App\Http\Requests\Api\V1\PaperRequest;
use App\Http\Requests\Api\V1\UpdatePaperRequest;
use App\Http\Resources\PaperResource;
use App\Models\Paper;
use App\Traits\Api\V1\Responses as V1Responses;
use Illuminate\Http\Response;

class Papers extends Controller
{
    use V1Responses;

    public function index()
    {
        $papers = Paper::orderBy('id', 'DESC')->paginate(10);

        return $this->successPaginated(data: PaperResource::collection($papers), status: Response::HTTP_OK, message: 'All documents.');

    }

    public function createPaper(PaperRequest $request)
    {
        $data = $request->all();

        $paper = Paper::create($data);

        return $this->success(status: Response::HTTP_OK, message: 'Document Created Successfully!!.', data: new PaperResource($paper));
    }

    public function updatePaper(UpdatePaperRequest $request)
    {
        $data = $request->all();
        $paper = Paper::where('id', $data['paper_id'])->first();
        $paper->update($data);

        return $this->success(status: Response::HTTP_OK, message: 'Paper Updated Successfully!!.', data: new PaperResource($paper));
    }

    public function deletePaper(GetPaperByIdRequest $request)
    {
        $data = $request->all();
        $paper = Paper::find($data['paper_id']);

        if (is_null($paper)) {
            return $this->error(status: Response::HTTP_OK, message: 'Paper Not Found!!.');
        }

        $paper->delete();
        return $this->success(status: Response::HTTP_OK, message: 'Paper Deleted Successfully!!.', data: new PaperResource($paper));
    }

    public function getPaperById(GetPaperByIdRequest $request)
    {
        $data = $request->all();
        $paper = Paper::where('id', $data['paper_id'])->first();

        return $this->success(status: Response::HTTP_OK, message: 'Paper Details.', data: new PaperResource($paper));
    }
}
