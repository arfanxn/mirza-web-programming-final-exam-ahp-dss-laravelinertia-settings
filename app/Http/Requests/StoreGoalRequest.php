<?php

namespace App\Http\Requests;

use App\Enums\Criterion\ImpactType;
use Illuminate\Validation\Rule;

class StoreGoalRequest extends AuthenticatableRequest
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
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'string'],
            'title' => ['nullable', 'string', 'min:2', 'max:128'],
            'description' => ['nullable', 'string', 'max:256'],

            /* // ! Deprecated
            'criteria' => ['nullable', 'array', 'min:2'],
            'criteria.*.name' => ['required', 'string', 'min:2', 'max:128'],
            'criteria.*.impact_type' => [
                'required',
                Rule::in(array_merge(ImpactType::getValues(), [true, false]))
            ],
            'criteria.*.index' => ['required', 'numeric', 'digits_between:0,6'],
            'criteria.*.weight' => ['required', 'numeric', 'digits_between:1,9'],

            'alternatives' => ['nullable', 'array', 'min:2'],
            'alternatives.*.name' => ['required', 'string', 'min:2', 'max:128'],
            'alternatives.*.index' => ['required', 'numeric', 'digits_between:0,6'],

            'performance_scores' => ['nullable', 'array'],
            'performance_scores.*.criterion' => ['required', 'array'],
            'performance_scores.*.criterion.index' => ['required', 'numeric', 'digits_between:0,6'],
            'performance_scores.*.alternative' => ['required', 'array'],
            'performance_scores.*.alternative.index' => ['required', 'numeric', 'digits_between:0,6'],
            'performance_scores.*.value' => ['required', 'numeric'],

            'pairwise_comparisons' => ['nullable', 'array'],
            'pairwise_comparisons.*.primary_criterion' => ['required', 'array'],
            'pairwise_comparisons.*.secondary_criterion' => ['required', 'array'],
            'pairwise_comparisons.*.primary_criterion.index' => ['required', 'numeric', 'digits_between:0,6'],
            'pairwise_comparisons.*.secondary_criterion.index' => ['required', 'numeric', 'digits_between:0,6'],
            'pairwise_comparisons.*.value' => ['required', 'numeric'],
            */
        ];
    }
}
