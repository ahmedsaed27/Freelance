<?php

namespace App\Http\Controllers\Api\V1\Skills;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\SkillsRequest;
use App\Models\Skill;
use App\Traits\Api\V1\Responses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;


class SkillsController extends Controller
{
    use Responses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Skill::paginate(10);

        return $this->successPaginated(status: Response::HTTP_OK, message: 'Skill Retrieved Successfully.', data: $data);
    }

    public function getAllDataWithoutPaginate(){
        $data = Skill::get();

        return $this->success(status: Response::HTTP_OK, message: 'Skill Retrieved Successfully.', data: $data);
    }


    public function getLogs(string $id){
        $data = Skill::find($id);


        if(!$data){
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: "Sorry, the requested data was not found."
            );
        }

        $logs = Activity::where('subject_id', $data->id)
                        ->where('subject_type', Skill::class)
                        ->get();


        if ($logs->isEmpty()) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: "No logs found for the specified Skill."
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
    public function store(SkillsRequest $request)
    {
        try{
            DB::beginTransaction();
            $data = Skill::create($request->validated());
            DB::commit();

            return $this->success(status: Response::HTTP_OK, message: 'Skill Retrieved Successfully.', data: $data);
        }catch(Exception $e){
            return $this->error(status:Response::HTTP_INTERNAL_SERVER_ERROR , message:$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Skill::find($id);

        if (!$data) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Skill not found.',);
        }

        return $this->success(status: Response::HTTP_OK, message: 'Skill Retrieved Successfully.', data:$data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SkillsRequest $request, string $id)
    {
        $data = Skill::find($id);

        if (!$data) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Skill not found.',);
        }
        DB::beginTransaction();

        $data->update($request->validated());

        DB::commit();


        return $this->success(status: Response::HTTP_OK, message: 'Skill Updated Successfully.', data:$data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Skill::find($id);

        if (!$data) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Skill not found.',);
        }

        $data->delete();

        return $this->success(status: Response::HTTP_OK, message: 'Skill Deleted Successfully.', data: $data);
    }

    public function restore(string $id)
    {
        $data = Skill::withTrashed()->find($id);

        if (!$data) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: 'Skill not found.'
            );
        }

        if (!$data->trashed()) {

            return $this->error(
                status: Response::HTTP_BAD_REQUEST,
                message: 'Skill not found.'
            );
        }

        $data->restore();

        return $this->success(
            status:Response::HTTP_OK
            ,message: 'Skill restored successfully.'
            , data: $data
        );
    }

    public function getAllTrashedData(){
        $data = Skill::onlyTrashed()->paginate(10);

        return $this->successPaginated(
            status:Response::HTTP_OK
            , message:'Skill Retrived Succesfuly'
            , data: $data
        );
    }
}
