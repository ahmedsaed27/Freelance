<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class BookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'description' => 'required',
            'date' => 'required|date|after:today',
            'start_time' => 'required|date_format:H:i:s',
            'end_time' =>  'required|date_format:H:i:s|after:start_time',
            'profile_id' => 'required|exists:profiles,id'
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


    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation error',
            'errors' => $validator->errors(),
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}
