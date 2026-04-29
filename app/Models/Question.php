<?php

namespace App\Models;

use App\Models\Model;
use App\Models\Answer;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Question extends Model
{
    protected $fillable = [
        'body',
    ];

    public function deck(): BelongsTo
    {
        return $this->belongsTo(Deck::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'binded', 'tag_bind');
    }
}
