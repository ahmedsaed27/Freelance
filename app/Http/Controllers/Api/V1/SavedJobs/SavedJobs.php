<?php

namespace App\Http\Controllers\Api\V1\SavedJobs;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\SavedJobs as V1SavedJobs;
use App\Models\SavedJobs as SavedJobsModel;
use App\Traits\Api\V1\Responses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class SavedJobs extends Controller
{
    use Responses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = SavedJobsModel::with('user')->paginate(10);

        return $this->success(status:Response::HTTP_OK , message:'Job Retrieved Successfully' , data:$data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(V1SavedJobs $request)
    {
        try{
            $request->merge([
                'user_id' => auth()->guard('api')->id()
            ]);

            DB::beginTransaction();

            $case = SavedJobsModel::create($request->validate());

            DB::commit();

            return $this->success(status:Response::HTTP_OK , message:'Job Saved Successfully' , data:$case);
        }catch(Exception $e){
            DB::rollBack();

            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: $e->getMessage());

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $job = SavedJobsModel::with('user')->where('user_id' , auth()->guard('api')->id())->where('id' , $id)->first();

        if (!$job) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Job not found.',);
        }

        return $this->success(status: Response::HTTP_OK, message: 'Job Retrieved Successfully.', data: $job);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
       //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $job = SavedJobsModel::where('user_id' , auth()->guard('api')->id())->where('id' , $id)->first();

        if(!$job){
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Job not found.',);
        }

        $job->delete();

        return $this->success(status: Response::HTTP_OK, message: 'Job Removed From Saved Successfully.', data: $job);
    }
}
