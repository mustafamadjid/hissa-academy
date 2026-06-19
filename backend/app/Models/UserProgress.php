<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'user_id',
    'lesson_id',
    'last_position_seconds',
    'total_watched_seconds',
    'status',
    'completed_at',
])]
class UserProgress extends Model
{
    /** @use HasFactory<\Database\Factories\UserProgressFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'user_progress';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
        ];
    }
}
