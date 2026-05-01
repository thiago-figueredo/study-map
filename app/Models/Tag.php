<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Tag extends Model
{
    protected $fillable = [
        'name',
    ];

    public function quizzes(): MorphToMany
    {
        return $this->morphedByMany(Quiz::class, 'binded', 'tag_bind');
    }
}
