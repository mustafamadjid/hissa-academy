<?php

namespace App\Features\Quizz\Models;

use App\Models\Concerns\HasUuidPrimaryKey;
use Database\Factories\Features\Quizz\Models\AnswerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'question_id',
    'answer',
    'is_correct',
])]
class Answer extends Model
{
    /** @use HasFactory<AnswerFactory> */
    use HasFactory, HasUuidPrimaryKey;

    protected $table = 'answers_options';

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
        ];
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
