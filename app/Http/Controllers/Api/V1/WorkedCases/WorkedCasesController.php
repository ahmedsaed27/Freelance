<?php

namespace App\Http\Controllers\Api\V1\WorkedCases;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\WorkedCaseRequest;
use App\Models\WorkedCases;
use App\Traits\Api\V1\Responses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;


class WorkedCasesController extends Controller
{
    use Responses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = WorkedCases::paginate(10);

        return $this->successPaginated(status: Response::HTTP_OK, message: 'WorkedCases Retrieved Successfully.', data: $data);
    }

    public function getAllDataWithoutPaginate(){
        $data = WorkedCases::get();

        return $this->success(status: Response::HTTP_OK, message: 'WorkedCases Retrieved Successfully.', data: $data);
    }


    public function getLogs(string $id){
        $data = WorkedCases::find($id);

        if(!$data){
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: "Sorry, the requested data was not found."
            );
        }

        $logs = Activity::where('subject_id', $data->id)
                        ->where('subject_type', WorkedCases::class)
                        ->get();

        if ($logs->isEmpty()) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: "No logs found for the specified WorkedCases."
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
    public function store(WorkedCaseRequest $request)
    {
        try{
            DB::beginTransaction();
            $data = WorkedCases::create($request->validated());
            DB::commit();

            return $this->success(status: Response::HTTP_OK, message: 'WorkedCases Retrieved Successfully.', data: $data);
        }catch(Exception $e){
            return $this->error(status:Response::HTTP_INTERNAL_SERVER_ERROR , message:$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = WorkedCases::find($id);

        if (!$data) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'WorkedCases not found.',);
        }

        return $this->success(status: Response::HTTP_OK, message: 'WorkedCases Retrieved Successfully.', data:$data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WorkedCaseRequest $request, string $id)
    {
        $data = WorkedCases::find($id);

        if (!$data) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'WorkedCases not found.',);
        }
        DB::beginTransaction();

        $data->update($request->validated());

        DB::commit();


        return $this->success(status: Response::HTTP_OK, message: 'WorkedCases Updated Successfully.', data:$data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = WorkedCases::find($id);

        if (!$data) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'WorkedCases not found.',);
        }

        $data->delete();

        return $this->success(status: Response::HTTP_OK, message: 'WorkedCases Deleted Successfully.', data: $data);
    }

     public function restore(string $id)
    {
        $data = WorkedCases::withTrashed()->find($id);

        if (!$data) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: 'WorkedCases not found.'
            );
        }

        if (!$data->trashed()) {

            return $this->error(
                status: Response::HTTP_BAD_REQUEST,
                message: 'WorkedCases not found.'
            );
        }

        $data->restore();

        return $this->success(
            status:Response::HTTP_OK
            ,message: 'WorkedCases restored successfully.'
            , data: $data
        );
    }

    public function getAllTrashedData(){
        $data = WorkedCases::onlyTrashed()->paginate(10);

        return $this->successPaginated(
            status:Response::HTTP_OK
            , message:'WorkedCases Retrived Succesfuly'
            , data: $data
        );
    }
}
