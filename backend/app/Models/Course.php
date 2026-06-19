<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'course_name',
    'description',
    'minimum_score',
    'status'
])]
class Course extends Model
{
    /** @use HasFactory<\Database\Factories\CourseFactory> */
    use HasFactory;
    use SoftDeletes;
    protected static function booted(): void
    {
        static::creating(function (Course $course): void {
            $course->public_uuid ??= (string) Str::uuid();
        });
    }
}
