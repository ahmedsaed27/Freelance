<?php

namespace App\Http\Controllers\Api\V1\Currency;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CurrencyRequest;
use App\Models\Currency;
use App\Traits\Api\V1\Responses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class CurrencyController extends Controller
{
    use Responses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Currency::paginate(10);

        return $this->successPaginated(status: Response::HTTP_OK, message: 'Currency Retrieved Successfully.', data: $data);
    }

    public function getAllDataWithoutPaginate(){
        $data = Currency::get();

        return $this->success(status: Response::HTTP_OK, message: 'Currency Retrieved Successfully.', data: $data);
    }


    public function getLogs(string $id){
        $data = Currency::find($id);

        if(!$data){
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: "Sorry, the requested data was not found."
            );
        }

        $logs = Activity::where('subject_id', $data->id)
                        ->where('subject_type', Currency::class)
                        ->get();

        if ($logs->isEmpty()) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: "No logs found for the specified Currency."
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
    public function store(CurrencyRequest $request)
    {
        try{
            DB::beginTransaction();
            $data = Currency::create($request->validated());
            DB::commit();

            return $this->success(status: Response::HTTP_OK, message: 'Currency Retrieved Successfully.', data: $data);
        }catch(Exception $e){
            return $this->error(status:Response::HTTP_INTERNAL_SERVER_ERROR , message:$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Currency::find($id);

        if (!$data) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Currency not found.',);
        }

        return $this->success(status: Response::HTTP_OK, message: 'Currency Retrieved Successfully.', data:$data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CurrencyRequest $request, string $id)
    {
        $data = Currency::find($id);

        if (!$data) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Currency not found.',);
        }
        DB::beginTransaction();

        $data->update($request->validated());

        DB::commit();


        return $this->success(status: Response::HTTP_OK, message: 'Currency Updated Successfully.', data:$data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Currency::find($id);

        if (!$data) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Currency not found.',);
        }

        $data->delete();

        return $this->success(status: Response::HTTP_OK, message: 'Currency Deleted Successfully.', data: $data);
    }

     public function restore(string $id)
    {
        $data = Currency::withTrashed()->find($id);

        if (!$data) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: 'Currency not found.'
            );
        }

        if (!$data->trashed()) {

            return $this->error(
                status: Response::HTTP_BAD_REQUEST,
                message: 'Currency not found.'
            );
        }

        $data->restore();

        return $this->success(
            status:Response::HTTP_OK
            ,message: 'Currency restored successfully.'
            , data: $data
        );
    }

    public function getAllTrashedData(){
        $data = Currency::onlyTrashed()->paginate(10);

        return $this->successPaginated(
            status:Response::HTTP_OK
            , message:'Currency Retrived Succesfuly'
            , data: $data
        );
    }
}
