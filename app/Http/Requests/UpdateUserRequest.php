<?php

namespace App\Http\Requests;

use App\Traits\FailedValidationRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends AuthenticatableRequest
{
    use FailedValidationRequest;

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
            'name' => ['required', 'min:2'],
            'email' => ['nullable', 'email'],
            'current_password' => ['nullable', 'min:8'],
            'new_password' => [
                'nullable',
                'required_with:current_password',
                'min:8'
            ],
            'new_password_confirmation' => ['required_with:new_password', 'same:new_password'],
        ];
    }
}
