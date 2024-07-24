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
        $imageRules = ['image'];
        $cvRules = ['mimes:pdf'];
        $unionCardRules = ['image'];
        $certificateRules = ['file', 'mimes:pdf'];

        if ($this->isMethod('post')) {
            array_unshift($imageRules, 'required');
            array_unshift($cvRules, 'required');
            array_unshift($unionCardRules, 'required');
            array_unshift($certificateRules, 'required');
        }

        return [
            // main data
            'address' => 'required',
            'areas_of_expertise' => 'required',
            'hourly_rate' => 'required|numeric',
            'image' => $imageRules,
            'cv' => $cvRules,
            'union_card' => $unionCardRules,
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

            // socials
            'socials' => 'required|array',
            'socials.instagram' => 'required|url',
            'socials.linkedin' => 'required|url',
            'socials.facebook' => 'required|url',

            // education
            'education' => 'required|array',
            'education.*.qualification' => 'required|string',
            'education.*.university' => 'required|string',
            'education.*.specialization' => 'required|in:appeal',
            'education.*.countries_id' => 'required|exists:user_db.cities,id',
            'education.*.additional_information' => 'nullable',
            'education.*.certificate' => $certificateRules,

            // work experience
            'work_experience' => 'required|array',
            'work_experience.*.job_name' => 'required|string',
            'work_experience.*.countries_id' => 'required|exists:user_db.cities,id',
            'work_experience.*.section' => 'required|in:personal_status',
            'work_experience.*.specialization' => 'required|in:civil_law',
            'work_experience.*.job_type' => 'required|in:fullTime,partTime,freelance',
            'work_experience.*.work_place' => 'required|in:office,house,flexible',
            'work_experience.*.responsibilities' => 'required|string',
            'work_experience.*.career_level' => 'required|in:boss,expert,mid_level,junior,student',
            'work_experience.*.from' => 'required|date',
            'work_experience.*.to' => 'required|date|after:work_experience.*.from',
            'work_experience.*.certificate' => $certificateRules,
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
            'field.in' => 'The selected field is invalid.',
            'specialization.required' => 'The specialization is required.',
            'specialization.in' => 'The selected specialization is invalid.',
            'level.required' => 'The level is required.',
            'level.in' => 'The selected level is invalid.',

            // socials
            'socials.required' => 'The socials field is required.',
            'socials.array' => 'The socials must be an array.',
            'socials.instagram.required' => 'The Instagram URL is required.',
            'socials.instagram.url' => 'The Instagram URL must be a valid URL.',
            'socials.linkedin.required' => 'The LinkedIn URL is required.',
            'socials.linkedin.url' => 'The LinkedIn URL must be a valid URL.',
            'socials.facebook.required' => 'The Facebook URL is required.',
            'socials.facebook.url' => 'The Facebook URL must be a valid URL.',

            // education
            'education.required' => 'The education field is required.',
            'education.array' => 'The education must be an array.',
            'education.*.qualification.required' => 'The qualification field is required for each education entry.',
            'education.*.university.required' => 'The university field is required for each education entry.',
            'education.*.specialization.required' => 'The specialization field is required for each education entry.',
            'education.*.specialization.in' => 'The selected specialization for education is invalid.',
            'education.*.countries_id.required' => 'The country field is required for each education entry.',
            'education.*.countries_id.exists' => 'The selected country for education is invalid.',
            'education.*.certificate.required' => 'The certificate field is required for each education entry.',
            'education.*.certificate.file' => 'The certificate must be a valid file.',
            'education.*.certificate.mimes' => 'The certificate must be a file of type: pdf.',

            // work experience
            'work_experience.required' => 'The work experience field is required.',
            'work_experience.array' => 'The work experience must be an array.',
            'work_experience.*.job_name.required' => 'The job name field is required for each work experience entry.',
            'work_experience.*.countries_id.required' => 'The country field is required for each work experience entry.',
            'work_experience.*.countries_id.exists' => 'The selected country for work experience is invalid.',
            'work_experience.*.section.required' => 'The section field is required for each work experience entry.',
            'work_experience.*.section.in' => 'The selected section for work experience is invalid.',
            'work_experience.*.specialization.required' => 'The specialization field is required for each work experience entry.',
            'work_experience.*.specialization.in' => 'The selected specialization for work experience is invalid.',
            'work_experience.*.job_type.required' => 'The job type field is required for each work experience entry.',
            'work_experience.*.job_type.in' => 'The selected job type for work experience is invalid.',
            'work_experience.*.work_place.required' => 'The work place field is required for each work experience entry.',
            'work_experience.*.work_place.in' => 'The selected work place for work experience is invalid.',
            'work_experience.*.responsibilities.required' => 'The responsibilities field is required for each work experience entry.',
            'work_experience.*.career_level.required' => 'The career level field is required for each work experience entry.',
            'work_experience.*.career_level.in' => 'The selected career level for work experience is invalid.',
            'work_experience.*.from.required' => 'The from date is required for each work experience entry.',
            'work_experience.*.from.date' => 'The from date must be a valid date.',
            'work_experience.*.to.required' => 'The to date is required for each work experience entry.',
            'work_experience.*.to.date' => 'The to date must be a valid date.',
            'work_experience.*.to.after' => 'The to date must be after the from date for each work experience entry.',
            'work_experience.*.certificate.required' => 'The certificate field is required for each work experience entry.',
            'work_experience.*.certificate.file' => 'The certificate must be a valid file.',
            'work_experience.*.certificate.mimes' => 'The certificate must be a file of type: pdf.',
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
