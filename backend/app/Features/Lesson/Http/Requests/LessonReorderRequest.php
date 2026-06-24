<?php
namespace App\Features\Lesson\Http\Requests;

use App\Features\Lesson\DTOs\LessonUpdateData;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LessonReorderRequest extends FormRequest
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
            'lessons' => ['required', 'array', 'min:1'],
            'lessons.*.id' => ['required','uuid','distinct'],
            'lessons.*.position' => ['required', 'integer', 'min:1','distinct'],
            
        ];
    }

    
}
?>