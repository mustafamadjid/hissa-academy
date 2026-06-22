<?php

namespace App\Features\Quizz\Models;

use App\Features\Course\Models\Course;
use App\Models\Concerns\HasUuidPrimaryKey;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'course_id',
    'quiz_name',
    'is_active',
])]
class Quizz extends Model
{
    /** @use HasFactory<\Database\Factories\Features\Quizz\Models\QuizzFactory> */
    use HasFactory, HasUuidPrimaryKey, SoftDeletes;

    protected $table = 'quizzs';

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
