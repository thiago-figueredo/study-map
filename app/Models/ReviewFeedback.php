<?php

namespace App\Models;

use App\Enums\ReviewFeedback as ReviewFeedbackEnum;
use Illuminate\Database\Eloquent\Model;

class ReviewFeedback extends Model
{
    protected $table = 'review_feedbacks';

    protected $fillable = [
        'question_id',
        'feedback',
        'easy_factor',
        'interval',
        'repetitions',
        'next_review_date',
    ];

    protected $casts = [
        'feedback' => ReviewFeedbackEnum::class,
    ];
}
