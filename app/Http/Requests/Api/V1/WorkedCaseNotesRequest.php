<?php

namespace App\Http\Requests\Api\V1;

use App\Rules\UserBelongsToProfile;
use App\Rules\UserBelongToCase;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class WorkedCaseNotesRequest extends FormRequest
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
            'worked_case_id' => 'required|exists:worked_cases,id',
            'created_by_user_id' =>[
                'required',
                'exists:user_db.users,id',
                new UserBelongToCase($this->worked_case_id , $this->created_by_user_id),
            ],
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:worked_case_notes,id',
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
            'worked_case_id.required' => 'The worked case ID is required.',
            'worked_case_id.exists' => 'The selected worked case ID does not exist.',
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
