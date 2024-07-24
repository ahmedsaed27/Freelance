<?php

namespace App\Http\Controllers\Api\V1\keywords;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\KeywordsRequest;
use App\Models\keyword;
use App\Traits\Api\V1\Responses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class KeywordsController extends Controller
{
    use Responses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = keyword::paginate(10);

        return $this->successPaginated(status: Response::HTTP_OK, message: 'Keyword Retrieved Successfully.', data: $data);
    }

    public function getAllDataWithoutPaginate(){
        $data = Keyword::get();

        return $this->success(status: Response::HTTP_OK, message: 'Keyword Retrieved Successfully.', data: $data);
    }


    public function getLogs(string $id){
        $data = Keyword::find($id);

        if(!$data){
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: "Sorry, the requested data was not found."
            );
        }

        $logs = Activity::where('subject_id', $data->id)
                        ->where('subject_type', Keyword::class)
                        ->get();

        if ($logs->isEmpty()) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: "No logs found for the specified Keyword."
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
    public function store(KeywordsRequest $request)
    {
        try{
            DB::beginTransaction();
            $data = Keyword::create($request->validated());
            DB::commit();

            return $this->success(status: Response::HTTP_OK, message: 'Keyword Retrieved Successfully.', data: $data);
        }catch(Exception $e){
            return $this->error(status:Response::HTTP_INTERNAL_SERVER_ERROR , message:$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Keyword::find($id);

        if (!$data) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Keyword not found.',);
        }

        return $this->success(status: Response::HTTP_OK, message: 'Keyword Retrieved Successfully.', data:$data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(KeywordsRequest $request, string $id)
    {
        $data = Keyword::find($id);

        if (!$data) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Keyword not found.',);
        }
        DB::beginTransaction();

        $data->update($request->validated());

        DB::commit();


        return $this->success(status: Response::HTTP_OK, message: 'Keyword Updated Successfully.', data:$data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Keyword::find($id);

        if (!$data) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Keyword not found.',);
        }

        $data->delete();

        return $this->success(status: Response::HTTP_OK, message: 'Keyword Deleted Successfully.', data: $data);
    }

     public function restore(string $id)
    {
        $data = Keyword::withTrashed()->find($id);

        if (!$data) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: 'Keyword not found.'
            );
        }

        if (!$data->trashed()) {

            return $this->error(
                status: Response::HTTP_BAD_REQUEST,
                message: 'Keyword not found.'
            );
        }

        $data->restore();

        return $this->success(
            status:Response::HTTP_OK
            ,message: 'Keyword restored successfully.'
            , data: $data
        );
    }

    public function getAllTrashedData(){
        $data = Keyword::onlyTrashed()->paginate(10);

        return $this->successPaginated(
            status:Response::HTTP_OK
            , message:'Keyword Retrived Succesfuly'
            , data: $data
        );
    }
}
