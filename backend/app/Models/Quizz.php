<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'course_id',
    'quiz_name',
    'is_active',
])]
class Quizz extends Model
{
    /** @use HasFactory<\Database\Factories\QuizzFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'quizzs';

    protected static function booted(): void
    {
        static::creating(function (Quizz $quizz): void {
            $quizz->public_uuid ??= (string) Str::uuid();
        });
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
