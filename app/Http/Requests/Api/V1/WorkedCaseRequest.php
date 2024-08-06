<?php

namespace App\Http\Requests\Api\V1;

use App\Rules\OwnerTheCase;
use App\Rules\UniqueProfileCase;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class WorkedCaseRequest extends FormRequest
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
            'profile_id' => 'required|exists:profiles,id',
            'case_id' => [
                'required',
                'exists:cases,id',
                new OwnerTheCase($this->case_id),
                new UniqueProfileCase($this->profile_id, $this->case_id),
            ],
            // 'rate' => 'required|numeric',
            // 'currency_id' => 'required|exists:currencies,id',
            // 'status' => 'required|string|in:Pending,In Progress,Completed',
            // 'start_time' => 'required|date',
            // 'end_time' => 'nullable|date|after_or_equal:start_time',
            'is_paid' => 'required|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'profile_id.required' => 'The profile ID is required.',
            'profile_id.exists' => 'The selected profile ID does not exist.',
            'case_id.required' => 'The case ID is required.',
            'case_id.exists' => 'The selected case ID does not exist.',
            'rate.required' => 'The rate is required.',
            'rate.numeric' => 'The rate must be a numeric value.',
            'currency_id.required' => 'The currency ID is required.',
            'currency_id.exists' => 'The selected currency ID does not exist.',
            'status.required' => 'The status is required.',
            'status.string' => 'The status must be a string.',
            'status.in' => 'The status must be in Pending , Completed , In Progress.',
            'start_time.required' => 'The start time is required.',
            'start_time.date' => 'The start time must be a valid date.',
            'end_time.date' => 'The end time must be a valid date.',
            'end_time.after_or_equal' => 'The end time must be after or equal to the start time.',
            'is_paid.required' => 'The payment status is required.',
            'is_paid.boolean' => 'The payment status must be true or false.',
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
