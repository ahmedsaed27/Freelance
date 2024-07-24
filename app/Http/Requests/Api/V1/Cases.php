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
        $rules = [
            'skills' => 'required|array',
            'skills.*' => 'exists:skills,id',
            'keywords' => 'required|array',
            'keywords.*' => 'exists:keywords,id',
            'type_id' => 'required|exists:types,id',
            'is_visible' => 'required|boolean',
            'title' => 'required|string',
            'specialization' => 'required|string',
            'currency_id' => 'required|exists:currencies,id',
            'min_amount' => 'required|numeric',
            'max_amount' => 'required|numeric',
            'country_id' => 'required|exists:user_db.countries,id',
            'city_id' => 'required|exists:user_db.cities,id',
            'description' => 'required|string',
            'status' => 'required|in:Opened,Assigned'
        ];

        if ($this->isMethod('post')) {
            $rules['id'] = 'required|image';
            $rules['certificate'] = 'required|file|mimes:pdf';
        } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['id'] = 'nullable|image';
            $rules['certificate'] = 'nullable|file|mimes:pdf';
        }

        return $rules;
    }



    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation error',
            'errors' => $validator->errors(),
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}
