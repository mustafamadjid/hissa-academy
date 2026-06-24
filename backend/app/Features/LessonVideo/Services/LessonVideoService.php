<?php

namespace App\Features\LessonVideo\Services;

use App\Features\LessonVideo\DTOs\LessonVideoData;
use App\Features\LessonVideo\Exceptions\LessonVideoOperationException;
use DateInterval;
use Illuminate\Support\Facades\Http;
use Throwable;

final class LessonVideoService
{
    public function getVideoMetadata(string $videoId): LessonVideoData
    {
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
    }

    private function requiredString(array $item, string $key): string
    {
        $value = data_get($item, $key);

        if (! is_string($value) || $value === '') {
            throw new LessonVideoOperationException('Metadata video tidak lengkap.');
        }

        return $value;
    }

    private function thumbnailUrl(array $item): string
    {
        foreach (['maxres', 'standard', 'high', 'medium', 'default'] as $quality) {
            $url = data_get($item, "snippet.thumbnails.{$quality}.url");

            if (is_string($url) && $url !== '') {
                return $url;
            }
        }

        throw new LessonVideoOperationException('Metadata video tidak lengkap.');
    }

    private function durationSeconds(string $durationIso): int
    {
        try {
            $duration = new DateInterval($durationIso);
        } catch (Throwable $exception) {
            throw new LessonVideoOperationException('Metadata video tidak lengkap.', $exception);
        }

        return ($duration->d * 86400)
            + ($duration->h * 3600)
            + ($duration->i * 60)
            + $duration->s;
    }
}
