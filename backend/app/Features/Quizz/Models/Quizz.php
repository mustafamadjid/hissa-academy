<?php

namespace App\Features\Quizz\Models;

use App\Features\Course\Models\Course;
use App\Models\Concerns\HasUuidPrimaryKey;
use Database\Factories\Features\Quizz\Models\QuizzFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'course_id',
    'quiz_name',
    'is_active',
])]
class Quizz extends Model
{
    /** @use HasFactory<QuizzFactory> */
    use HasFactory, HasUuidPrimaryKey, SoftDeletes;

    protected $table = 'quizzs';

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }
}
