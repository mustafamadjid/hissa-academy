<?php

namespace App\Features\User\Http\Resources;

use App\Features\User\DTOs\UserProfileData;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class UserProfileResource extends JsonResource
{
    /**
     * @return array<string, string|null>
     */
    public function toArray(Request $request): array
    {
        /** @var UserProfileData $profile */
        $profile = $this->resource;

        return [
            'email' => $profile->email,
            'full_name' => $profile->fullName,
            'avatar_url' => $profile->avatarUrl,
            'role' => $profile->role,
        ];
    }
}
