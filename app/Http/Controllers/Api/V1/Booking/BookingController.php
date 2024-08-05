<?php

namespace App\Http\Controllers\Api\V1\Booking;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\BookingRequest;
use App\Http\Requests\Api\V1\ChangeBookingStatusRequest;
use App\Http\Requests\Api\V1\GetBookingByIdRequest;
use App\Http\Requests\Api\V1\UpdateBookingRequest;
use App\Http\Resources\BookingResource;
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
        // IMS Token 
        $data = Booking::with(['user', 'profile'])->paginate(10);

        return $this->successPaginated(status: Response::HTTP_OK, message: 'Bookings Retrieved Successfuly', data: BookingResource::collection($data));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function create(BookingRequest $request)
    {
        try {
            DB::beginTransaction();

            $request->merge([
                'user_id' => auth()->guard('api')->id(),
            ]);

            $data = Booking::create($request->all());

            DB::commit();

            return $this->success(status: Response::HTTP_OK, message: 'Booking Created Successfuly', data: new BookingResource($data));
        } catch (Exception $e) {
            DB::rollBack();

            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(GetBookingByIdRequest $request)
    {
        $data = Booking::with(['user', 'profile'])->find($request->booking_id);
        $user = auth()->guard('api')->user();
        $profile_id = $user->profile->id;

        if (!$data) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Booking not found.');
        }

        if ($user->id != $data->user_id || $profile_id != $data->profile_id) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'You Can Not View This Booking.');
        }

        return $this->success(status: Response::HTTP_OK, message: 'Booking Retrieved Successfuly', data: new BookingResource($data));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookingRequest $request)
    {
        try {
            $data = Booking::find($request->booking_id);

            if (!$data) {
                return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Booking not found.');
            }

            DB::beginTransaction();

            $data->update($request->all());

            DB::commit();

            $data->load('user', 'profile');
            return $this->success(status: Response::HTTP_OK, message: 'Booking Update Successfuly', data: new BookingResource($data));
        } catch (Exception $e) {
            DB::rollBack();

            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: $e->getMessage());
        }
    }

    public function changeStatus(ChangeBookingStatusRequest $request)
    {
        try {
            $data = Booking::find($request->booking_id);

            if (!$data) {
                return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Booking not found.');
            }

            DB::beginTransaction();

            $data->update($request->all());

            DB::commit();

            $data->load('user', 'profile');
            return $this->success(status: Response::HTTP_OK, message: 'Booking Status Update Successfuly', data: new BookingResource($data));
        } catch (Exception $e) {
            DB::rollBack();

            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: $e->getMessage());
        }
    }

    public function getAllUserBooking()
    {
        $user_id = auth()->guard('api')->id();
        $bookings = Booking::with(['user', 'profile'])->where('user_id', $user_id)->paginate(10);

        return $this->successPaginated(status: Response::HTTP_OK, message: 'Bookings Retrieved Successfuly', data: BookingResource::collection($bookings));
    }

    public function getAllProfileBooking()
    {
        $user = auth()->guard('api')->user();
        $profile_id = $user->profile->id;
        $bookings = Booking::with(['user', 'profile'])->where('profile_id', $profile_id)->paginate(10);

        return $this->successPaginated(status: Response::HTTP_OK, message: 'Bookings Retrieved Successfuly', data: BookingResource::collection($bookings));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(GetBookingByIdRequest $request)
    {
        $data = Booking::find($request->booking_id);
        $user = auth()->guard('api')->user();

        if (!$data) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'Booking not found.');
        }

        if ($data->status != 'Pending') {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'You Can Not Delete This Booking Because The status is ' . "[" . $data->status . "]");
        }

        if ($user->id != $data->user_id) {
            return $this->error(status: Response::HTTP_INTERNAL_SERVER_ERROR, message: 'You Can Not Delete This Booking.');
        }

        $data->delete();
        $data->load('user', 'profile');

        return $this->success(status: Response::HTTP_OK, message: 'Booking Deleted Successfuly', data: $data);
    }

    public function getAllTrashedDataByToken()
    {
        $user_id = auth()->guard('api')->id();
        $data = Booking::where('user_id', $user_id)->with(['user', 'profile'])->onlyTrashed()->paginate(10);

        return $this->successPaginated(
            status: Response::HTTP_OK,
            message: 'Booking Retrived Succesfuly',
            data: BookingResource::collection($data)
        );
    }
}