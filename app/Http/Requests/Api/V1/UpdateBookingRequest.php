<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Booking;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class UpdateBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $booking = Booking::where('id', $this->booking_id)->first();
        $user_id = auth()->guard('api')->id();

        // Ensure the logged-in user is the owner of the booking record
        return $booking && $booking->user_id === $user_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'booking_id' => 'required|exists:bookings,id',
            'title' => 'required|string',
            'description' => 'required',
            'date' => 'required|date|after:today',
            'start_time' => 'required|date_format:H:i:s',
            'end_time' => 'required|date_format:H:i:s|after:start_time',
        ];
    }

    protected function prepareForValidation()
    {
        $startTime = Carbon::createFromFormat('H:i:s', $this->start_time);
        $endTime = Carbon::createFromFormat('H:i:s', $this->end_time);

        $hours = $endTime->diffInHours($startTime);

        $this->merge([
            'hours' => $hours,
        ]);
    }

    protected function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $booking = Booking::where('id', $this->route('booking_id'))->first();

            if ($booking) {
                if ($booking->status === 'Accepted' || $booking->status === 'Rejected') {
                    $validator->errors()->add('status', 'Cannot update a booking with status "Accepted" or "Rejected".');
                }

                if ($booking->isPaid) {
                    $validator->errors()->add('isPaid', 'Cannot update a booking that is paid.');
                }
            }
        });
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation error',
            'errors' => $validator->errors(),
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}
