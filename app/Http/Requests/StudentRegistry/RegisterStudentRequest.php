<?php

namespace App\Http\Requests\StudentRegistry;

use Illuminate\Foundation\Http\FormRequest;

class RegisterStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'secret_key' => [
                'required',
                'string',
            ],

            'username' => [
                'required',
                'string',
                'max:100',
            ],

            'student_custom_id' => [
                'required',
                'string',
                'max:50',
            ],

            'institute_code' => [
                'required',
                'string',
                'max:20',
                // The institute must register itself first (register-institute)
                'exists:institutes,code',
            ],

            'status' => [
                'nullable',
                'boolean',
            ],

        ];
    }

    public function messages(): array
    {
        return [
            'institute_code.exists' => 'Unknown institute. Call register-institute first.',
        ];
    }
}