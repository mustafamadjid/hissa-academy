<?php

namespace App\Features\LessonVideo\Models;

use App\Features\Lesson\Models\Lesson;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'lesson_id',
    'video_url',
    'duration_seconds',
])]
class LessonVideo extends Model
{
    /** @use HasFactory<\Database\Factories\Features\LessonVideo\Models\LessonVideoFactory> */
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::creating(function (LessonVideo $lessonVideo): void {
            $lessonVideo->public_uuid ??= (string) Str::uuid();
        });
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
