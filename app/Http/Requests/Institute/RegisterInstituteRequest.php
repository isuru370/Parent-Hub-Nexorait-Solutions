<?php

namespace App\Http\Requests\Institute;

use Illuminate\Foundation\Http\FormRequest;

class RegisterInstituteRequest extends FormRequest
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

            'institute_code' => [
                'required',
                'string',
                'max:20',
            ],

            'institute_name' => [
                'required',
                'string',
                'max:255',
            ],

            'api_url' => [
                'required',
                'url',
            ],

            'contact_email' => [
                'nullable',
                'email',
            ],

            'contact_mobile' => [
                'nullable',
                'string',
                'max:20',
            ],

            'app_version' => [
                'nullable',
                'string',
                'max:50',
            ],

        ];
    }
}
