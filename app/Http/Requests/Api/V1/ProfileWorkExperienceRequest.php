<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class ProfileWorkExperienceRequest extends FormRequest
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
            // work experience
            // 'work_experience' => 'required|array',
            'company' => 'required|string',
            'job_title' => 'required|string',
            'country_id' => 'required|exists:user_db.countries,id',
            'job_type' => 'required|in:fullTime,partTime,freelance',
            'work_place' => 'required|in:office,house,flexible',
            'responsibilities' => 'required|string',
            'career_level' => 'required|in:boss,expert,mid_level,junior,student',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:work_experience.*.start_date',
            'certificate' => $certificateRules,
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
