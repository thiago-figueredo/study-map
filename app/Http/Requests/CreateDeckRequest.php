<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateDeckRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['string', 'max:50'],
            'tags' => ['array', 'nullable', 'max:10'],
            'tags.*' => ['string', 'unique:tags,name'],
            'questions' => ['array', 'nullable', 'max:10'],
            'questions.*.body' => ['string', 'max:255'],
            'questions.*.tags' => ['array', 'nullable', 'max:10'],
            'questions.*.tags.*' => ['string', 'max:50'],
            'questions.*.answers' => ['array', 'nullable', 'max:10'],
            'questions.*.answers.*.body' => ['string', 'max:255'],
            'questions.*.answers.*.is_correct' => ['boolean'],
        ];
    }
}
