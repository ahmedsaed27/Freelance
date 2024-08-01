<?php

namespace App\Http\Requests\Api\V1;

use App\Rules\UserBelongsToProfile;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class CaseProfileNotesRequest extends FormRequest
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
    public function rules()
    {
        return [
            'case_profile_id' => [
                'required',
                'exists:case_profile,id',
                new UserBelongsToProfile($this->case_profile_id , auth()->guard('api')->id())
            ],
            // 'created_by_user_id' => [
            //     'required',
            //     'exists:user_db.users,id',
                // new UserBelongsToProfile($this->case_profile_id ,  $this->created_by_user_id),
            // ],
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:case_profile_notes,id',
            'files' => 'nullable|array',
            'files.*' => 'file|mimes:png,jpg,pdf,xlsb,xltx'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'profile_id.required' => 'The profile ID is required.',
            'profile_id.exists' => 'The selected profile ID does not exist.',
            'created_by_user_id.required' => 'The user ID who created the note is required.',
            'created_by_user_id.exists' => 'The selected user ID does not exist.',
            'content.required' => 'The content of the note is required.',
            'content.string' => 'The content must be a string.',
            'parent_id.exists' => 'The selected parent note ID does not exist.',
            'files.array' => 'The files must be an array.',
            'files.*.file' => 'Each file must be a valid file.',
            'files.*.mimes' => 'Each file must be of type: png, jpg, pdf, xlsb, xltx.',
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
