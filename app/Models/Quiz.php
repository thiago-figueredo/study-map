<?php

namespace App\Models;

use App\Models\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Quiz extends Model
{
    protected $fillable = [
        'name',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'binded', 'tag_bind');
    }
}
