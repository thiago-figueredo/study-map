<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Arr;

class Model extends EloquentModel
{
    use HasTimestamps;
    use HasFactory;
    
    public static function make(array $attributes = []): static
    {
        return new static($attributes);
    }
}