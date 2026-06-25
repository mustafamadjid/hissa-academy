<?php

namespace App\Features\Student\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class StudentDetailResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $student = $this->resource['student'];

        return [
            'id' => $student->id,
            'full_name' => $student->full_name,
            'email' => $student->email,
            'phone' => $student->phone,
            'birth_date' => $student->birth_date?->toDateString(),
            'avatar_url' => $student->avatar_url,
            'last_login_at' => $student->last_login_at?->toISOString(),
            'email_verified_at' => $student->email_verified_at?->toISOString(),
            'role' => [
                'id' => $student->role?->id,
                'name' => $student->role?->name,
            ],
            'learning_summary' => $this->resource['summary'],
        ];
    }
}
