<?php

namespace App\Http\Controllers\Api\V1\WorkedCaseNotes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\WorkedCaseNotesRequest;
use App\Models\WorkedCaseNotes;
use App\Traits\Api\V1\Responses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;


class WorkedCaseNotesController extends Controller
{
    use Responses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = WorkedCaseNotes::paginate(10);

        return $this->successPaginated(status: Response::HTTP_OK, message: 'WorkedCaseNotes Retrieved Successfully.', data: $data);
    }

    public function getAllDataWithoutPaginate(){
        $data = WorkedCaseNotes::get();

        return $this->success(status: Response::HTTP_OK, message: 'WorkedCaseNotes Retrieved Successfully.', data: $data);
    }


    public function getLogs(string $id){
        $data = WorkedCaseNotes::find($id);

        if(!$data){
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: "Sorry, the requested data was not found."
            );
        }

        $logs = Activity::where('subject_id', $data->id)
                        ->where('subject_type', WorkedCaseNotes::class)
                        ->get();

        if ($logs->isEmpty()) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: "No logs found for the specified WorkedCaseNotes."
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
    public function store(WorkedCaseNotesRequest $request)
    {
        try{
            DB::beginTransaction();
            $data = WorkedCaseNotes::create($request->validated());

            if($request->hasFile('files')){
                foreach ($request->file('files') as $file) {
                    $data->addMedia($file)->toMediaCollection('worked_case_notes', 'worked_case_notes');
                }
            }


            DB::commit();

            return $this->success(status: Response::HTTP_OK, message: 'WorkedCaseNotes Retrieved Successfully.', data: $data);
        }catch(Exception $e){
            return $this->error(status:Response::HTTP_INTERNAL_SERVER_ERROR , message:$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = WorkedCaseNotes::find($id);

        if (!$data) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'WorkedCaseNotes not found.',);
        }

        return $this->success(status: Response::HTTP_OK, message: 'WorkedCaseNotes Retrieved Successfully.', data:$data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WorkedCaseNotesRequest $request, string $id)
    {
        $data = WorkedCaseNotes::find($id);

        if (!$data) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'WorkedCaseNotes not found.',);
        }
        DB::beginTransaction();

        $data->update($request->validated());

        if($request->hasFile('files')){
            $data->clearMediaCollection('worked_case_notes');
            foreach ($request->file('files') as $file) {
                $data->addMedia($file)->toMediaCollection('worked_case_notes', 'worked_case_notes');
            }
        }

        DB::commit();


        return $this->success(status: Response::HTTP_OK, message: 'WorkedCaseNotes Updated Successfully.', data:$data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = WorkedCaseNotes::find($id);

        if (!$data) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'WorkedCaseNotes not found.',);
        }

        $data->clearMediaCollection('worked_case_notes');
        $data->delete();

        return $this->success(status: Response::HTTP_OK, message: 'WorkedCaseNotes Deleted Successfully.', data: $data);
    }

     public function restore(string $id)
    {
        $data = WorkedCaseNotes::withTrashed()->find($id);

        if (!$data) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: 'WorkedCaseNotes not found.'
            );
        }

        if (!$data->trashed()) {

            return $this->error(
                status: Response::HTTP_BAD_REQUEST,
                message: 'WorkedCaseNotes not found.'
            );
        }

        $data->restore();

        return $this->success(
            status:Response::HTTP_OK
            ,message: 'WorkedCaseNotes restored successfully.'
            , data: $data
        );
    }

    public function getAllTrashedData(){
        $data = WorkedCaseNotes::onlyTrashed()->paginate(10);

        return $this->successPaginated(
            status:Response::HTTP_OK
            , message:'WorkedCaseNotes Retrived Succesfuly'
            , data: $data
        );
    }
}
