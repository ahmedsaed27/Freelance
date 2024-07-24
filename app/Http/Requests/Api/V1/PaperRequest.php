<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class PaperRequest extends FormRequest
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
            'name' => 'required|unique:papers,name',
            'description' => 'required',
            'data_type' => 'required|in:email,number,string,file',
            'is_unique' => 'required|in:0,1',
            'is_required' => 'required|in:0,1',
        ];
    }


    public function messages(): array
    {
        return [
            'data_type.in'   => 'The data_type must be one of the following options: email, number, string, file.',
            'is_unique.in'   => 'The is_unique must be one of the following options: 0, 1',
            'is_required.in' => 'The is_required must be one of the following options: 0, 1',
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
