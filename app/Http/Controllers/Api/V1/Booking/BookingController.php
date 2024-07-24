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


class BookingController extends Controller
{
    use Responses;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Booking::with('user')->paginate(10);

        return $this->success(status:Response::HTTP_OK , message:'Bookings Retrieved Successfuly' , data: $data);
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
}
