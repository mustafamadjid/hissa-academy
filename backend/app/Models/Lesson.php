<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'title',
    'position',
    'is_required'
])]
class Lesson extends Model
{
    /** @use HasFactory<\Database\Factories\LessonFactory> */
    use HasFactory;
    use SoftDeletes;
    protected static function booted(): void
    {
        static::creating(function (Lesson $lesson): void {
            $lesson->public_uuid ??= (string) Str::uuid();
        });
    }
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
