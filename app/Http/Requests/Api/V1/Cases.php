<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class Cases extends FormRequest
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
            'required_skills' => 'sometimes',
            'is_visible' => 'required|boolean',
            'freelance_type' => 'required|string',
            'title' => 'required|string',
            'specialization' => 'required|string',
            'proposed_budget' => 'required|numeric',
            'keywords' => 'required|array',
            'countries_id'=> 'required|exists:user_db.countries,id',
            'cities_id' => 'required|exists:user_db.cities,id',
            'notes' => 'required|string',
            'currency' => 'required|string',
            'id' => 'required|image',
            'certificate' => 'required|file|mimes:pdf'
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
