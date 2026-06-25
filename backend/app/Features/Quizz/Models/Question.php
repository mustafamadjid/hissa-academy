<?php

namespace App\Features\Quizz\Models;

use App\Models\Concerns\HasUuidPrimaryKey;
use Database\Factories\Features\Quizz\Models\QuestionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'quizz_id',
    'question',
    'position',
    'image_url',
])]
class Question extends Model
{
    /** @use HasFactory<QuestionFactory> */
    use HasFactory, HasUuidPrimaryKey;

    public function quizz(): BelongsTo
    {
        return $this->belongsTo(Quizz::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }
}
