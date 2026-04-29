<?php

namespace App\Models;

class TagBind extends Model
{
    protected $table = 'tag_bind';

    protected $fillable = [
        'tag_id',
        'binded_id',
        'binded_type',
    ];
}
