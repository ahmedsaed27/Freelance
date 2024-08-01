<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Profiles;
use App\Traits\Api\V1\Responses;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class VerificatioRequest extends FormRequest
{
    use Responses;
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
            'verified_at' => 'required|date|date_format:Y-m-d',
            'is_paid' => 'required|boolean',
            'start_date' => 'required|date|date_format:Y-m-d',
            'end_date' => 'nullable|date|date_format:Y-m-d',
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
