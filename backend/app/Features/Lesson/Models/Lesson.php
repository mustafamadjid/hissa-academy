<?php

namespace App\Features\Lesson\Models;

use App\Features\Course\Models\Course;
use App\Features\LessonVideo\Models\LessonVideo;
use App\Models\Concerns\HasUuidPrimaryKey;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'course_id',
    'title',
    'position',
    'is_required',
])]
class Lesson extends Model
{
    /** @use HasFactory<\Database\Factories\Features\Lesson\Models\LessonFactory> */
    use HasFactory, HasUuidPrimaryKey, SoftDeletes;

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function video(): HasOne
    {
        return $this->hasOne(LessonVideo::class);
    }

    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'is_required' => 'boolean',
        ];
    }
}
