<?php

namespace App\Http\Controllers\Api\V1\CaseProfileNotes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CaseProfileNotesRequest;
use App\Models\CaseProfileNotes;
use App\Models\CasesProfile;
use App\Traits\Api\V1\Responses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class CaseProfileNotesController extends Controller
{
    use Responses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = CaseProfileNotes::with('caseProfile' , 'createdBy' , 'parent')->paginate(10);

        return $this->successPaginated(status: Response::HTTP_OK, message: 'CaseProfileNotes Retrieved Successfully.', data: $data);
    }

    public function getAllDataWithoutPaginate(){
        $data = CaseProfileNotes::with('caseProfile' , 'createdBy' , 'parent')->get();

        return $this->success(status: Response::HTTP_OK, message: 'CaseProfileNotes Retrieved Successfully.', data: $data);
    }


    public function getLogs(string $id){
        $data = CaseProfileNotes::find($id);

        if(!$data){
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: "Sorry, the requested data was not found."
            );
        }

        $logs = Activity::where('subject_id', $data->id)
                        ->where('subject_type', CaseProfileNotes::class)
                        ->get();

        if ($logs->isEmpty()) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: "No logs found for the specified CaseProfileNotes."
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
    public function store(CaseProfileNotesRequest $request)
    {
        try{
            $user_id = auth()->guard('api')->id();

            DB::beginTransaction();

            $request->merge([
                'created_by_user_id' => $user_id,
            ]);

            $data = CaseProfileNotes::create($request->except('files'));

            if($request->hasFile('files')){
                foreach ($request->file('files') as $file) {
                    $data->addMedia($file)
                    ->withCustomProperties(['column' => 'files'])
                    ->toMediaCollection('profile_case_note', 'profile_case_note');
                }
            }

            DB::commit();
            return $this->success(status: Response::HTTP_OK, message: 'CaseProfileNotes Retrieved Successfully.', data: $data);
        }catch(Exception $e){
            return $this->error(status:Response::HTTP_INTERNAL_SERVER_ERROR , message:$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = CaseProfileNotes::find($id);

        if (!$data) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'CaseProfileNotes not found.',);
        }

        return $this->success(status: Response::HTTP_OK, message: 'CaseProfileNotes Retrieved Successfully.', data:$data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CaseProfileNotesRequest $request, string $id)
    {
        $data = CaseProfileNotes::find($id);
        $user_id = auth()->guard('api')->id();

        if (!$data) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'CaseProfileNotes not found.',);
        }

        if($data->created_by_user_id != $user_id){
            return $this->error(status:Response::HTTP_INTERNAL_SERVER_ERROR ,message:'Only Profile Owner Can Update The Note.');
        }

        DB::beginTransaction();

        $request->merge([
            'created_by_user_id' => $user_id,
        ]);

        $data->update($request->except('files'));

        if($request->hasFile('files')){
            $data->clearMediaCollection('profile_case_note');
            foreach ($request->file('files') as $file) {
                $data->addMedia($file)
                ->withCustomProperties(['column' => 'files'])
                ->toMediaCollection('profile_case_note', 'profile_case_note');
            }
        }

        DB::commit();


        return $this->success(status: Response::HTTP_OK, message: 'CaseProfileNotes Updated Successfully.', data:$data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = CaseProfileNotes::find($id);

        if (!$data) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'CaseProfileNotes not found.',);
        }
        $data->clearMediaCollection('profile_case_note');
        $data->delete();

        return $this->success(status: Response::HTTP_OK, message: 'CaseProfileNotes Deleted Successfully.', data: $data);
    }

     public function restore(string $id)
    {
        $data = CaseProfileNotes::withTrashed()->find($id);

        if (!$data) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: 'CaseProfileNotes not found.'
            );
        }

        if (!$data->trashed()) {

            return $this->error(
                status: Response::HTTP_BAD_REQUEST,
                message: 'CaseProfileNotes not found.'
            );
        }

        $data->restore();

        return $this->success(
            status:Response::HTTP_OK
            ,message: 'CaseProfileNotes restored successfully.'
            , data: $data
        );
    }

    public function getAllTrashedData(){
        $data = CaseProfileNotes::onlyTrashed()->paginate(10);

        return $this->successPaginated(
            status:Response::HTTP_OK
            , message:'CaseProfileNotes Retrived Succesfuly'
            , data: $data
        );
    }
}
