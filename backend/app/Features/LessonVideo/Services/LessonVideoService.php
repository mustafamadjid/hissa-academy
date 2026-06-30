<?php

namespace App\Features\LessonVideo\Services;

use App\Features\LessonVideo\DTOs\LessonVideoData;
use App\Features\LessonVideo\Exceptions\LessonVideoOperationException;
use DateInterval;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

final class LessonVideoService
{
    public function getVideoMetadata(string $videoId): LessonVideoData
    {
        try {
            $response = Http::get(
                'https://www.googleapis.com/youtube/v3/videos',
                [
                    'part' => 'snippet,contentDetails,status',
                    'id' => $videoId,
                    'key' => config('services.youtube.api_key'),
                ]
            );

            if ($response->failed()) {
                throw new LessonVideoOperationException('Gagal mengambil metadata video.');
            }

            $item = $response->json('items.0');

            if (! is_array($item)) {
                throw new LessonVideoOperationException('Video tidak ditemukan.');
            }

            return new LessonVideoData(
                videoId: $this->requiredString($item, 'id'),
                title: $this->requiredString($item, 'snippet.title'),
                description: (string) data_get($item, 'snippet.description', ''),
                channelTitle: $this->requiredString($item, 'snippet.channelTitle'),
                thumbnailUrl: $this->thumbnailUrl($item),
                durationIso: $this->requiredString($item, 'contentDetails.duration'),
                durationSeconds: $this->durationSeconds($this->requiredString($item, 'contentDetails.duration')),
                privacyStatus: $this->requiredString($item, 'status.privacyStatus'),
            );
        } catch (LessonVideoOperationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('Gagal mengambil metadata video dari YouTube.', [
                'video_id' => $videoId,
                'exception' => $exception,
            ]);

            throw new LessonVideoOperationException('Gagal mengambil metadata video.', $exception);
        }
    }

    private function requiredString(array $item, string $key): string
    {
        try {
            $value = data_get($item, $key);

            if (! is_string($value) || $value === '') {
                throw new LessonVideoOperationException('Metadata video tidak lengkap.');
            }

            return $value;
        } catch (LessonVideoOperationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('Gagal mengambil field metadata video.', [
                'key' => $key,
                'exception' => $exception,
            ]);

            throw new LessonVideoOperationException('Metadata video tidak lengkap.', $exception);
        }
    }

    private function thumbnailUrl(array $item): string
    {
        try {
            foreach (['maxres', 'standard', 'high', 'medium', 'default'] as $quality) {
                $url = data_get($item, "snippet.thumbnails.{$quality}.url");

                if (is_string($url) && $url !== '') {
                    return $url;
                }
            }

            throw new LessonVideoOperationException('Metadata video tidak lengkap.');
        } catch (LessonVideoOperationException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            Log::error('Gagal mengambil thumbnail video.', [
                'exception' => $exception,
            ]);

            throw new LessonVideoOperationException('Metadata video tidak lengkap.', $exception);
        }
    }

    private function durationSeconds(string $durationIso): int
    {
        try {
            $duration = new DateInterval($durationIso);
        } catch (Throwable $exception) {
            Log::error('Gagal mengonversi durasi video.', [
                'duration_iso' => $durationIso,
                'exception' => $exception,
            ]);

            throw new LessonVideoOperationException('Metadata video tidak lengkap.', $exception);
        }

        return ($duration->d * 86400)
            + ($duration->h * 3600)
            + ($duration->i * 60)
            + $duration->s;
    }
}
