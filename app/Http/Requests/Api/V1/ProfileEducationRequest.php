<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class ProfileEducationRequest extends FormRequest
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
        $certificateRules = ['file', 'mimes:pdf'];

        if ($this->isMethod('post')) {
            array_unshift($certificateRules, 'required');
        }

        return [
            // education
            'education' => 'required|array',
            'education.*.major' => 'required|string',
            'education.*.grade' => 'required|string',
            'education.*.degree' => 'required|string',
            'education.*.qualification' => 'sometimes|string',
            'education.*.university' => 'required|string',
            'education.*.country_id' => 'required|exists:user_db.countries,id',
            'education.*.additional_information' => 'nullable',
            'education.*.start_date' => 'required|date',
            'education.*.end_date' => 'required|date|after:education.*.start_date',
            'education.*.certificate' => $certificateRules,
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