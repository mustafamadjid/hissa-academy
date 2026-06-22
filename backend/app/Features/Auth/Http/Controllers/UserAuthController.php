<?php

namespace App\Features\Auth\Http\Controllers;

use App\Features\Auth\Exceptions\InvalidCredentialsException;
use App\Features\Auth\Http\Requests\LoginRequest;
use App\Features\Auth\Services\UserAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class UserAuthController
{
    public function store(
        LoginRequest $request,
        UserAuthService $authService,
    ): JsonResponse {
        try {
            $user = $authService->login($request->toDTO());
            $request->session()->regenerate();

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil.',
                'data' => [
                    'user' => $user,
                ],
            ], 200);
        } catch (InvalidCredentialsException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 422);
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server.',
            ], 500);
        }

    }

    public function destroy(
        Request $request,
        UserAuthService $authService
    ): JsonResponse {
        try {
            $authService->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'message' => 'Logout berhasil.',
            ]);
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server.',
            ], 500);
        }

    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(
            $request->user()->load('role')
        );
    }
}
