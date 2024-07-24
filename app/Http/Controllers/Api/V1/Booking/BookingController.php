<?php

namespace App\Http\Controllers\Api\V1\Booking;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Booking as BookingRequest;
use App\Models\Booking;
use App\Traits\Api\V1\Responses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class BookingController extends Controller
{
    use Responses;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Booking::with('user')->paginate(10);

        return $this->successPaginated(status:Response::HTTP_OK , message:'Bookings Retrieved Successfuly' , data: $data);
    }

    public function getAllDataWithoutPaginate(){
        $data = Booking::with('user')->get();

        return $this->success(status: Response::HTTP_OK, message: 'Bookings Retrieved Successfully.', data: $data);
    }

    public function getLogs(string $id){
        $data = Booking::find($id);

        if(!$data){
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: "Sorry, the requested data was not found."
            );
        }

        $logs = Activity::where('subject_id', $data->id)
                        ->where('subject_type', Booking::class)
                        ->get();

        if ($logs->isEmpty()) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: "No logs found for the specified Booking."
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
    public function store(BookingRequest $request)
    {
        try{
            DB::beginTransaction();

            $request->merge([
                'user_id' => auth()->guard('api')->id(),
            ]);

            $data = Booking::create($request->all());

            DB::commit();

            return $this->success(status:Response::HTTP_OK , message:'Booking Created Successfuly' , data: $data);


        }catch(Exception $e){
            DB::rollBack();

            return $this->error(status:Response::HTTP_INTERNAL_SERVER_ERROR , message:$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Booking::with('user')->find($id);

        if(!$data){
            return $this->error(status:Response::HTTP_INTERNAL_SERVER_ERROR , message:'Booking not found.');
        }

        return $this->success(status:Response::HTTP_OK , message:'Booking Retrieved Successfuly' , data: $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BookingRequest $request, string $id)
    {
        try{
            $data = Booking::find($id);

            if(!$data){
                return $this->error(status:Response::HTTP_INTERNAL_SERVER_ERROR , message:'Booking not found.');
            }

            DB::beginTransaction();

            $request->merge([
                'user_id' => auth()->guard('api')->id(),
            ]);

            $data->update($request->all());

            DB::commit();

            return $this->success(status:Response::HTTP_OK , message:'Booking Update Successfuly' , data: $data);


        }catch(Exception $e){
            DB::rollBack();

            return $this->error(status:Response::HTTP_INTERNAL_SERVER_ERROR , message:$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Booking::find($id);

        if(!$data){
            return $this->error(status:Response::HTTP_INTERNAL_SERVER_ERROR , message:'Booking not found.');
        }

        $data->delete();

        return $this->success(status:Response::HTTP_OK , message:'Booking Deleted Successfuly' , data: $data);
    }

    public function restore(string $id)
    {
        $data = Booking::withTrashed()->find($id);

        if (!$data) {
            return $this->error(
                status: Response::HTTP_NOT_FOUND,
                message: 'Booking not found.'
            );
        }

        if (!$data->trashed()) {

            return $this->error(
                status: Response::HTTP_BAD_REQUEST,
                message: 'Booking not found.'
            );
        }

        $data->restore();

        return $this->success(
            status:Response::HTTP_OK
            ,message: 'Document restored successfully.'
            , data: $data
        );
    }

    public function getAllTrashedData(){
        $data = Booking::onlyTrashed()->paginate(10);

        return $this->successPaginated(
            status:Response::HTTP_OK
            , message:'Booking Retrived Succesfuly'
            , data: $data
        );
    }
}
