<?php

namespace App\Features\Course\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CourseStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'course_name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'minimum_score' => ['required', 'numeric', 'min:0', 'max:100'],
            'status' => ['required', 'string', 'max:255'],
        ];
    }
}
