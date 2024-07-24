<?php

namespace App\Http\Controllers\Api\V1\Types;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\TypeRequest;
use App\Models\Type;
use App\Traits\Api\V1\Responses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class TypesController extends Controller
{
    use Responses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Type::paginate(10);

        return $this->successPaginated(status: Response::HTTP_OK, message: 'Type Retrieved Successfully.', data: $data);
    }

    public function getAllDataWithoutPaginate(){
        $data = Type::get();

        return $this->success(status: Response::HTTP_OK, message: 'Type Retrieved Successfully.', data: $data);
    }

    public function getLogs(string $id){
        $data = Type::find($id);

        if(!$data){
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: "Sorry, the requested data was not found."
            );
        }

        $logs = Activity::where('subject_id', $data->id)
                        ->where('subject_type', Type::class)
                        ->get();

        if ($logs->isEmpty()) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: "No logs found for the specified Type."
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
    public function store(TypeRequest $request)
    {
        try{
            DB::beginTransaction();
            $data = Type::create($request->validated());
            DB::commit();

            return $this->success(status: Response::HTTP_OK, message: 'Type Retrieved Successfully.', data: $data);
        }catch(Exception $e){
            return $this->error(status:Response::HTTP_INTERNAL_SERVER_ERROR , message:$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Type::find($id);

        if (!$data) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Type not found.',);
        }

        return $this->success(status: Response::HTTP_OK, message: 'Type Retrieved Successfully.', data:$data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TypeRequest $request, string $id)
    {
        $data = Type::find($id);

        if (!$data) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Type not found.',);
        }
        DB::beginTransaction();

        $data->update($request->validated());

        DB::commit();


        return $this->success(status: Response::HTTP_OK, message: 'Type Updated Successfully.', data:$data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Type::find($id);

        if (!$data) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Type not found.',);
        }

        $data->delete();

        return $this->success(status: Response::HTTP_OK, message: 'Type Deleted Successfully.', data: $data);
    }

    public function restore(string $id)
    {
        $data = Type::withTrashed()->find($id);

        if (!$data) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: 'Type not found.'
            );
        }

        if (!$data->trashed()) {

            return $this->error(
                status: Response::HTTP_BAD_REQUEST,
                message: 'Type not found.'
            );
        }

        $data->restore();

        return $this->success(
            status:Response::HTTP_OK
            ,message: 'Type restored successfully.'
            , data: $data
        );
    }

    public function getAllTrashedData(){
        $data = Type::onlyTrashed()->paginate(10);

        return $this->successPaginated(
            status:Response::HTTP_OK
            , message:'Type Retrived Succesfuly'
            , data: $data
        );
    }
}
