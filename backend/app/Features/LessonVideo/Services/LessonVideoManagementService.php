<?php

namespace App\Features\LessonVideo\Services;

use App\Features\LessonVideo\Contracts\LessonVideoRepositoryContract;
use App\Features\LessonVideo\DTOs\LessonVideoUpsertData;
use App\Features\LessonVideo\Exceptions\LessonVideoOperationException;
use App\Features\LessonVideo\Models\LessonVideo;
use App\Features\User\Models\User;
use App\GlobalExceptions\AuthorizationException;
use App\Helper\EnsureAdminForService;
use Illuminate\Support\Facades\Log;
use Throwable;

final class LessonVideoManagementService
{
    public function __construct(
        private readonly LessonVideoRepositoryContract $lessonVideoRepository,
        private readonly LessonVideoService $youtubeMetadataService,
        private readonly EnsureAdminForService $ensureAdmin,
    ) {}

    public function saveMetadata(string $lessonId, LessonVideoUpsertData $data, ?User $actor): ?LessonVideo
    {
        $this->ensureAdmin->ensureAdmin($actor);

        try {
            $lesson = $this->lessonVideoRepository->findLessonById($lessonId);

            if ($lesson === null) {
                return null;
            }

            $metadata = $this->youtubeMetadataService->getVideoMetadata($data->youtubeVideoId);

            return $this->lessonVideoRepository->saveMetadata($lesson, $metadata);
        } catch (LessonVideoOperationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('Gagal menyimpan metadata video lesson.', [
                'lesson_id' => $lessonId,
                'actor_id' => $actor?->id,
                'exception' => $exception,
            ]);

            throw new LessonVideoOperationException('Gagal menyimpan metadata video lesson.', $exception);
        }
    }

    public function deleteMetadata(string $lessonId, ?User $actor): ?bool
    {
        $this->ensureAdmin->ensureAdmin($actor);

        try {
            $lesson = $this->lessonVideoRepository->findLessonById($lessonId);

            if ($lesson === null) {
                return null;
            }

            if ($lesson->course?->status !== 'draft') {
                throw new AuthorizationException('Metadata video hanya dapat dihapus dari lesson draft.');
            }

            return $this->lessonVideoRepository->deleteForLesson($lesson);
        } catch (AuthorizationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('Gagal menghapus metadata video lesson.', [
                'lesson_id' => $lessonId,
                'actor_id' => $actor?->id,
                'exception' => $exception,
            ]);

            throw new LessonVideoOperationException('Gagal menghapus metadata video lesson.', $exception);
        }
    }
}
