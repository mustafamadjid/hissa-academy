<?php

namespace App\Features\User\Http\Controllers;

use App\Features\User\Exceptions\UserProfileOperationException;
use App\Features\User\Http\Resources\UserProfileResource;
use App\Features\User\Services\UserProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class UserProfileController
{
    public function show(Request $request, UserProfileService $service): JsonResponse
    {
        try {
            $profile = $service->getCurrentProfile($request->user());

            return response()->json([
                'success' => true,
                'message' => 'Profil user berhasil diambil.',
                'data' => new UserProfileResource($profile),
            ]);
        } catch (UserProfileOperationException $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }
}
