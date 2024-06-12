<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\Api\V1\Types;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rules\Enum;

class Profiles extends FormRequest
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
            // main data
            'location' => 'required',
            'areas_of_expertise' => 'required',
            'hourly_rate' => 'required|numeric',
            'image' => 'required|image',
            'cv' => 'required|mimes:pdf',
            'union_card' => 'required|image',
            'years_of_experience' => 'required|numeric',
            'type' =>  [new Enum(Types::class)],

            'career' => 'required|string',
            'countries_id'=> 'required|exists:user_db.countries,id',
            'cities_id' => 'required|exists:user_db.cities,id',
            'field' => 'required',
            'specialization' => 'required',
            'experience' => 'required',

            // socials
            'socials' => 'required|array',
            'socials.instagram' => 'required|url',
            'socials.linkedin' => 'required|url',
            'socials.facebook' => 'required|url',

            // education
            'education' => 'required|array',
            'education.*.qualification' => 'required|string',
            'education.*.university' => 'required|string',
            'education.*.specialization' => 'required|string',
            'education.*.countries_id' => 'required|exists:user_db.cities,id',
            'education.*.additional_information' => 'nullable',
            'education.*.certificate' => 'required|file|mimes:pdf',



            // work experience
            'work_experience' => 'required|array',
            'work_experience.*.job_name' => 'required|string',
            'work_experience.*.countries_id' => 'required|exists:user_db.cities,id',
            'work_experience.*.section' => 'required|string',
            'work_experience.*.specialization' => 'required|string',
            'work_experience.*.job_type' => 'required|string',
            'work_experience.*.work_place' => 'required|string',
            'work_experience.*.responsibilities' => 'required|string',
            'work_experience.*.career_level' => 'required|string',
            'work_experience.*.from' => 'required|date',
            'work_experience.*.to' => 'required|date|after:education.*.from',
            'work_experience.*.certificate' => 'required|file|mimes:pdf',

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
