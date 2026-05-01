<?php

namespace App\Services;

use App\Enums\ReviewFeedback as ReviewFeedbackEnum;
use App\Http\Requests\ReviewRequest;
use App\Models\ReviewFeedback;

class ReviewService {
    public function review(ReviewRequest $request): void
    {
        $feedback = $request->validated('feedback');
        $feedback = ReviewFeedbackEnum::from($feedback);
        $question_id = $request->validated('question_id');

        $current = ReviewFeedback::query()->firstOrCreate(
            ['question_id' => $question_id],
            [
                'feedback' => $feedback,
                'easy_factor' => 2.5,
                'interval' => 0,
                'repetitions' => 0,
            ],
        );

        if ($feedback === ReviewFeedbackEnum::AGAIN) {
            $current->update([
                'feedback' => $feedback,
                'interval' => 1,
                'repetitions' => 1,
            ]);
        } else {
            $repetitions = $current->repetitions + 1;

            $interval = match (true) {
                $repetitions === 1 => 1,
                $repetitions === 2 => 6,
                default => (int) round($current->interval * $current->easy_factor),
            };

            $current->update([
                'feedback' => $feedback,
                'repetitions' => $repetitions,
                'interval' => $interval,
            ]);

            $this->updateEaseFactor($feedback, $current->fresh());
        }

        $this->updateNextReviewDate($current->fresh());
    }

    public function updateNextReviewDate(ReviewFeedback $current_review_feedback): void
    {
        $current_review_feedback->update([
            'next_review_date' => now()->addDays($current_review_feedback->interval)->format('Y-m-d'),
        ]);
    }

    private function updateEaseFactor(
        ReviewFeedbackEnum $feedback,
        ReviewFeedback $current_review_feedback
    ): void {
        $easy_factor = $current_review_feedback->easy_factor;
        $easy_factor += (0.1 - (5 - $feedback->value) * (0.08 + (5 - $feedback->value) * 0.02));

        if ($easy_factor < 1.3) {
            $easy_factor = 1.3;
        }

        $current_review_feedback->update(compact('easy_factor'));
    }
}
