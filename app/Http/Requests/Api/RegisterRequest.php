<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public endpoint
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            // Name
            'name.required' => 'Name is required.',
            'name.string'   => 'Name must be a valid string.',
            'name.max'      => 'Name may not be greater than 255 characters.',

            // Email
            'email.required' => 'Email address is required.',
            'email.string'   => 'Email must be a valid string.',
            'email.email'    => 'Please provide a valid email address.',
            'email.max'      => 'Email may not be greater than 255 characters.',
            'email.unique'   => 'This email address is already registered.',

            // Password
            'password.required'   => 'Password is required.',
            'password.string'     => 'Password must be a valid string.',
            'password.min'        => 'Password must be at least 6 characters.',
            'password.confirmed'  => 'Password confirmation does not match.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'code' => 422,
            'status' => false,
            'message' => $validator->errors()->first(),
            'data' => null,
        ], 422));
    }
}
