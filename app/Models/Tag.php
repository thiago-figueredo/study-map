<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Tag extends Model
{
    protected $fillable = [
        'name',
    ];

    public function decks(): MorphToMany
    {
        return $this->morphedByMany(Deck::class, 'binded', 'tag_bind');
    }
}
