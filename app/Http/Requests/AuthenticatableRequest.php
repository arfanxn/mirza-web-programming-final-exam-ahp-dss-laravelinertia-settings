<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class AuthenticatableRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    abstract public function authorize(): bool;

    public function prepareForValidation()
    {
        $this->merge([
            'user_id' => $this->user()->id
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    abstract public function rules(): array;
}
