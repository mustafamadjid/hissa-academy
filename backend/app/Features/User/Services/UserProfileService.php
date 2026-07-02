<?php

namespace App\Features\User\Services;

use App\Features\User\Contracts\UserRepositoryContract;
use App\Features\User\DTOs\UserProfileData;
use App\Features\User\Exceptions\UserProfileOperationException;
use App\Features\User\Models\User;
use Illuminate\Support\Facades\Log;
use Throwable;

final class UserProfileService
{
    public function __construct(
        private readonly UserRepositoryContract $userRepository,
    ) {}

    public function getCurrentProfile(User $actor): UserProfileData
    {
        try {
            $user = $this->userRepository->findProfileById($actor->id);

            if ($user === null) {
                throw new UserProfileOperationException;
            }

            return UserProfileData::fromModel($user);
        } catch (UserProfileOperationException $exception) {
            Log::error('Gagal mengambil profil user.', [
                'actor_id' => $actor->id,
                'exception' => $exception,
            ]);

            throw $exception;
        } catch (Throwable $exception) {
            Log::error('Gagal mengambil profil user.', [
                'actor_id' => $actor->id,
                'exception' => $exception,
            ]);

            throw new UserProfileOperationException(previous: $exception);
        }
    }
}
