<?php

namespace App\Models;

use App\Models\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Deck extends Model
{
    protected $fillable = [
        'name',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }
}
