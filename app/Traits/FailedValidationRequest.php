<?php

namespace App\Traits;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

trait FailedValidationRequest
{
    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson()) {
            $errors = $validator->errors();
            throw new HttpResponseException(response()->json([
                'message' => $errors->first(),
                'errors' => $errors
            ], 422));
        }
        parent::failedValidation($validator);
    }
}
