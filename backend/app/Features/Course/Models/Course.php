<?php

namespace App\Features\Course\Models;

use App\Models\Concerns\HasUuidPrimaryKey;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'course_name',
    'description',
    'minimum_score',
    'status'
])]
class Course extends Model
{
    /** @use HasFactory<\Database\Factories\Features\Course\Models\CourseFactory> */
    use HasFactory, HasUuidPrimaryKey, SoftDeletes;
}
