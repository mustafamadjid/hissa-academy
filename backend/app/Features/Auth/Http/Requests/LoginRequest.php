<?php

namespace App\Features\Auth\Http\Requests;

use App\Features\Auth\DTOs\LoginData;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['bail', 'required', 'email', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ];
    }

    public function toDTO(): LoginData
    {
        $validated = $this->validated();

        return new LoginData($validated['email'], $validated['password']);
    }
}
