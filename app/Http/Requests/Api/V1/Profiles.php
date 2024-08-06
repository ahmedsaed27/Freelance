<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\Api\V1\Types;
use App\Models\Profiles as ModelsProfiles;
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
    // public function rules(): array
    // {
    //     return [
    //         // main data
    //         'address' => 'required',
    //         'areas_of_expertise' => 'required',
    //         'hourly_rate' => 'required|numeric',
    //         'image' => 'required|image',
    //         'cv' => 'required|mimes:pdf',
    //         'union_card' => 'required|image',
    //         'years_of_experience' => 'required|numeric',
    //         'type_id' =>  'required|exists:types,id',
    //         'currency_id'=> 'required|exists:currencys,id',
    //         'career' => 'required|string',
    //         'country_id'=> 'required|exists:user_db.countries,id',
    //         'city_id' => 'required|exists:user_db.cities,id',
    //         'field' => 'required|in:appeal',
    //         'specialization' => 'required|in:appeal',
    //         'level' => 'required|in:boss,expert,mid_level,junior,student',

    //         // socials
    //         'socials' => 'required|array',
    //         'socials.instagram' => 'required|url',
    //         'socials.linkedin' => 'required|url',
    //         'socials.facebook' => 'required|url',

    //         // education
    //         'education' => 'required|array',
    //         'education.*.qualification' => 'required|string',
    //         'education.*.university' => 'required|string',
    //         'education.*.specialization' => 'required|in:appeal',
    //         'education.*.countries_id' => 'required|exists:user_db.cities,id',
    //         'education.*.additional_information' => 'nullable',
    //         'education.*.certificate' => 'required|file|mimes:pdf',



    //         // work experience
    //         'work_experience' => 'required|array',
    //         'work_experience.*.job_name' => 'required|string',
    //         'work_experience.*.countries_id' => 'required|exists:user_db.cities,id',
    //         'work_experience.*.section' => 'required|in:personal_status',
    //         'work_experience.*.specialization' => 'required|in:civil_law',
    //         'work_experience.*.job_type' => 'required|in:fullTime,partTime,freelance',
    //         'work_experience.*.work_place' => 'required|in:office,house,flexible',
    //         'work_experience.*.responsibilities' => 'required|string',
    //         'work_experience.*.career_level' => 'required|in:boss,expert,mid_level,junior,student',
    //         'work_experience.*.from' => 'required|date',
    //         'work_experience.*.to' => 'required|date|after:education.*.from',
    //         'work_experience.*.certificate' => 'required|file|mimes:pdf',

    //     ];
    // }

    public function rules(): array
    {
        $imageRules = ['sometimes','image'];
        $cvRules = ['mimes:pdf'];
        $certificateRules = ['file', 'mimes:pdf'];

        if ($this->isMethod('post')) {
            array_unshift($imageRules, 'required');
            array_unshift($cvRules, 'required');
            array_unshift($certificateRules, 'required');
        }

        return [
            // main data
            'address' => 'required',
            'areas_of_expertise' => 'required',
            'hourly_rate' => 'required|numeric',
            'image' => $imageRules,
            'cv' => $cvRules,
            'years_of_experience' => 'required|numeric',
            'types' => 'required|array',
            'types.*' => 'exists:types,id',
            'currency_id' => 'required|exists:currencies,id',
            'career' => 'required|string',
            'country_id' => 'required|exists:user_db.countries,id',
            'city_id' => 'required|exists:user_db.cities,id',
            'field' => 'required|in:appeal',
            'specialization' => 'required|in:appeal',
            'level' => 'required|in:boss,expert,mid_level,junior,student',
            'status' => 'sometimes|in:Under Review,Approved,Rejected',
        ];
    }

    public function messages(): array
    {
        return [
            // main data
            'address.required' => 'The address field is required.',
            'areas_of_expertise.required' => 'The areas of expertise field is required.',
            'hourly_rate.required' => 'The hourly rate field is required.',
            'hourly_rate.numeric' => 'The hourly rate must be a number.',
            'image.required' => 'The image field is required for new entries.',
            'image.image' => 'The image must be a valid image file.',
            'cv.required' => 'The CV field is required for new entries.',
            'cv.mimes' => 'The CV must be a file of type: pdf.',
            'union_card.required' => 'The union card field is required for new entries.',
            'union_card.image' => 'The union card must be a valid image file.',
            'years_of_experience.required' => 'The years of experience field is required.',
            'years_of_experience.numeric' => 'The years of experience must be a number.',
            'type_id.required' => 'The type field is required.',
            'type_id.exists' => 'The selected type is invalid.',
            'currency_id.required' => 'The currency field is required.',
            'currency_id.exists' => 'The selected currency is invalid.',
            'career.required' => 'The career field is required.',
            'career.string' => 'The career must be a string.',
            'country_id.required' => 'The country field is required.',
            'country_id.exists' => 'The selected country is invalid.',
            'city_id.required' => 'The city field is required.',
            'city_id.exists' => 'The selected city is invalid.',
            'field.required' => 'The field is required.',
            'field.in' => 'The field must be in appeal.',
            'specialization.required' => 'The specialization is required.',
            'specialization.in' => 'The selected specialization is invalid.',
            'level.required' => 'The level is required.',
            'level.in' => 'The selected status must be in Under Review, Approved, Rejected.',

        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation error',
            'errors' => $validator->errors(),
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }

    protected function failValidation(string $message)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation error',
            'errors' => $message
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }

}
