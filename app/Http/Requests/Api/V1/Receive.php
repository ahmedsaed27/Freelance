<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class Receive extends FormRequest
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
    public function rules()
    {
        return [
            'caseId' => 'required|exists:cases,id',
            'suggested_rate' => 'required|numeric',
            'description' => 'required|string',
            'status' => 'required|in:Pending,Accepted,Rejected',
            'estimation_time' => 'required|date',
        ];
    }

    public function messages()
    {
        return [
            'caseId.required' => 'The case ID is required.',
            'caseId.exists' => 'The selected case ID is invalid.',
            'suggested_rate.required' => 'The suggested rate is required.',
            'suggested_rate.numeric' => 'The suggested rate must be a number.',
            'description.required' => 'The description is required.',
            'status.required' => 'The status is required.',
            'status.in' => 'The status must be either Pending, Accepted, or Rejected.',
            'estimation_time.required' => 'The estimation time is required.',
            'estimation_time.date' => 'The estimation time must be a valid date.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation error',
            'errors' => $validator->errors(),
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}
