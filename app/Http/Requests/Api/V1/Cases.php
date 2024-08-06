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
            'max_amount' => 'required|numeric',
            'min_amount' => 'required|numeric|lte:max_amount',
            'country_id' => 'required|exists:user_db.countries,id',
            'city_id' => 'required|exists:user_db.cities,id',
            'description' => 'required|string',
            'status' => 'required|in:Opened,Assigned',
            'number_of_days' => 'required|integer',
            'is_anonymous' => 'sometimes|boolean'
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

    public function messages()
    {
        return [
            'skills.required' => 'The skills field is required.',
            'skills.array' => 'The skills field must be an array.',
            'skills.*.exists' => 'The selected skill is invalid.',
            'keywords.required' => 'The keywords field is required.',
            'keywords.array' => 'The keywords field must be an array.',
            'keywords.*.exists' => 'The selected keyword is invalid.',
            'type_id.required' => 'The type field is required.',
            'type_id.exists' => 'The selected type is invalid.',
            'is_visible.required' => 'The visibility field is required.',
            'is_visible.boolean' => 'The visibility field must be true or false.',
            'title.required' => 'The title field is required.',
            'title.string' => 'The title field must be a string.',
            'specialization.required' => 'The specialization field is required.',
            'specialization.string' => 'The specialization field must be a string.',
            'currency_id.required' => 'The currency field is required.',
            'currency_id.exists' => 'The selected currency is invalid.',
            'min_amount.required' => 'The minimum amount field is required.',
            'min_amount.numeric' => 'The minimum amount field must be a number.',
            'max_amount.required' => 'The maximum amount field is required.',
            'max_amount.numeric' => 'The maximum amount field must be a number.',
            'country_id.required' => 'The country field is required.',
            'country_id.exists' => 'The selected country is invalid.',
            'city_id.required' => 'The city field is required.',
            'city_id.exists' => 'The selected city is invalid.',
            'description.required' => 'The description field is required.',
            'description.string' => 'The description field must be a string.',
            'status.required' => 'The status field is required.',
            'status.in' => 'The status field must be one of the following values: Opened or Assigned.',
            'number_of_days.required' => 'The number_of_days field is required.',
            'number_of_days.integer' => 'The number_of_days field must be a integer.'
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
