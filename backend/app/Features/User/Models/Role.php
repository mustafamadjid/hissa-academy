<?php

namespace App\Features\User\Models;

use App\Models\Concerns\HasUuidPrimaryKey;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'name',
])]
class Role extends Model
{
    use HasFactory, HasUuidPrimaryKey, SoftDeletes;

    
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
