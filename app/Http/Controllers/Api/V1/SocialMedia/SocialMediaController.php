<?php

namespace App\Http\Controllers\Api\V1\SocialMedia;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\SocialMediaRequest;
use App\Models\SocialMedia;
use App\Traits\Api\V1\Responses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;


class SocialMediaController extends Controller
{
    use Responses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = SocialMedia::paginate(10);

        return $this->successPaginated(status: Response::HTTP_OK, message: 'SocialMedia Retrieved Successfully.', data: $data);
    }

    public function getAllDataWithoutPaginate(){
        $data = SocialMedia::get();

        return $this->success(status: Response::HTTP_OK, message: 'SocialMedia Retrieved Successfully.', data: $data);
    }


    public function getLogs(string $id){
        $data = SocialMedia::find($id);

        if(!$data){
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: "Sorry, the requested data was not found."
            );
        }

        $logs = Activity::where('subject_id', $data->id)
                        ->where('subject_type', SocialMedia::class)
                        ->get();

        if ($logs->isEmpty()) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: "No logs found for the specified SocialMedia."
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
    public function store(SocialMediaRequest $request)
    {
        try{
            DB::beginTransaction();
            $data = SocialMedia::create($request->validated());
            DB::commit();

            return $this->success(status: Response::HTTP_OK, message: 'SocialMedia Retrieved Successfully.', data: $data);
        }catch(Exception $e){
            return $this->error(status:Response::HTTP_INTERNAL_SERVER_ERROR , message:$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = SocialMedia::find($id);

        if (!$data) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'SocialMedia not found.',);
        }

        return $this->success(status: Response::HTTP_OK, message: 'SocialMedia Retrieved Successfully.', data:$data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SocialMediaRequest $request, string $id)
    {
        $data = SocialMedia::find($id);

        if (!$data) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'SocialMedia not found.',);
        }
        DB::beginTransaction();

        $data->update($request->validated());

        DB::commit();


        return $this->success(status: Response::HTTP_OK, message: 'SocialMedia Updated Successfully.', data:$data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = SocialMedia::find($id);

        if (!$data) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'SocialMedia not found.',);
        }

        $data->delete();

        return $this->success(status: Response::HTTP_OK, message: 'SocialMedia Deleted Successfully.', data: $data);
    }

     public function restore(string $id)
    {
        $data = SocialMedia::withTrashed()->find($id);

        if (!$data) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: 'SocialMedia not found.'
            );
        }

        if (!$data->trashed()) {

            return $this->error(
                status: Response::HTTP_BAD_REQUEST,
                message: 'SocialMedia not found.'
            );
        }

        $data->restore();

        return $this->success(
            status:Response::HTTP_OK
            ,message: 'SocialMedia restored successfully.'
            , data: $data
        );
    }

    public function getAllTrashedData(){
        $data = SocialMedia::onlyTrashed()->paginate(10);

        return $this->successPaginated(
            status:Response::HTTP_OK
            , message:'SocialMedia Retrived Succesfuly'
            , data: $data
        );
    }
}
