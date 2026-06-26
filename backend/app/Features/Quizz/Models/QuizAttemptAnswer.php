<?php

namespace App\Features\Quizz\Models;

use App\Models\Concerns\HasUuidPrimaryKey;
use Database\Factories\Features\Quizz\Models\QuizAttemptAnswerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'quiz_attempt_id',
    'question_id',
    'answer_option_id',
    'is_correct',
])]
class QuizAttemptAnswer extends Model
{
    /** @use HasFactory<QuizAttemptAnswerFactory> */
    use HasFactory, HasUuidPrimaryKey;

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(QuizAttempt::class, 'quiz_attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function answerOption(): BelongsTo
    {
        return $this->belongsTo(Answer::class, 'answer_option_id');
    }

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
        ];
    }
}
